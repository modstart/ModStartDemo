<?php


namespace ModStart\Form;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Exception\ResultException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\SortDirection;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\IdUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Detail\Detail;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Display;
use ModStart\Field\Select;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Concern\HasCascadeFields;
use ModStart\Form\Type\FormEngine;
use ModStart\Form\Type\FormMode;
use ModStart\Grid\Concerns\HasSort;
use ModStart\Repository\Filter\HasRepositoryFilter;
use ModStart\Repository\Filter\HasScopeFilter;
use ModStart\Repository\Repository;
use ModStart\Repository\RepositoryUtil;
use ModStart\Support\Concern\HasBuilder;
use ModStart\Support\Concern\HasFields;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;
use stdClass;



class Form implements Renderable
{
    use HasFields,
        HasBuilder,
        HasFluentAttribute,
        HasSort,
        HasCascadeFields,
        HasScopeFilter,
        HasRepositoryFilter;

    
    public $id;
    
    private $repository;

    
    private $view = 'modstart::core.form.index';

    protected $fluentAttributes = [
        'engine',
        'builder',
        'mode',
        'title',
        'showSubmit',
        'showReset',
        'itemId',
        'item',
        'hookSubmitted',
        'hookSaving',
        'hookSaved',
        'hookDeleting',
        'hookDeleted',
        'hookChanged',
        'hookResponse',
        'dataSubmitted',
        'dataForming',
        'dataAdding',
        'dataEditing',
        'canAdd',
        'canEdit',
        'canDelete',
        'canSort',
        'canCopy',
        'sortAddPosition',
        'formClass',
        'treeMaxLevel',
        'treeRootPid',
        'formUrl',
        'ajax',
        'formAttr',
    ];
    
    private $engine = 'basic';
    
    private $mode = 'form';
    
    private $title;
    
    private $showSubmit = true;
    
    private $showReset = true;
    
    private $itemId = null;
    
    private $item;
    
    private $hookSubmitted;
    
    private $hookSaving;
    
    private $hookSaved;
    
    private $hookDeleting;
    
    private $hookDeleted;
    
    private $hookChanged;
    
    private $hookResponse;
    
    private $dataSubmitted;
    
    private $dataForming;
    
    private $dataAdding;
    
    private $dataEditing;
    private $canAdd = true;
    private $canEdit = true;
    private $canDelete = true;
    private $canSort = false;
    private $canCopy = false;
    private $sortAddPosition = false;
    private $formClass = '';
    private $treeMaxLevel = 99;
    private $treeRootPid = 0;
    private $formUrl = null;
    private $ajax = true;
    private $formAttr = '';

    
    public function __construct($repository, \Closure $builder = null)
    {
        $this->id = IdUtil::generate('Grid');
        $this->repository = Repository::instance($repository);
        $this->setupFields();
        $this->fieldDefaultRenderMode(FieldRenderMode::FORM);
        $this->setupRepositoryFilter();
        $this->builder($builder);
    }

    public static function make($model = null, \Closure $builder = null)
    {
        if (
            is_object($model)
            ||
            (class_exists($model) && is_subclass_of($model, Model::class))
        ) {
            return new Form($model, $builder);
        }
        return new Form(DynamicModel::make($model), $builder);
    }

    public function asTree($keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = FormEngine::TREE;
        $this->canSort(true);
        return $this;
    }

    public function asTreeMass($rootPid = 0, $keyName = 'id', $pidColumn = 'pid', $sortColumn = 'sort', $titleColumn = 'title')
    {
        $this->repository->setKeyName($keyName);
        $this->repository->setTreePidColumn($pidColumn);
        $this->repository->setSortColumn($sortColumn);
        $this->repository->setTreeTitleColumn($titleColumn);
        $this->engine = FormEngine::TREE_MASS;
        $this->canSort(true);
        return $this;
    }

    private function build()
    {
        $this->runBuilder();
        if ($this->engine == FormEngine::TREE) {
            
            if ($this->treeMaxLevel > 1) {
                $field = FieldManager::make($this, 'select', $this->repository->getTreePidColumn(), L('Parent'));
                $field->optionRepositoryTreeItems($this->repository, $this->treeMaxLevel);
            } else {
                $field = FieldManager::make($this, 'hidden', $this->repository->getTreePidColumn(), L('Parent'));
                $field->value(0);
            }
            $this->prependField($field);
        } else if ($this->engine == FormEngine::TREE_MASS) {
            
            $field = FieldManager::make($this, 'display', $this->repository->getTreePidColumn(), L('Parent'));
            $field->addable(true)->editable(true)->listable(false);
            $field->hookRendering(function (AbstractField $field, $item, $index) {
                if (empty($item)) {
                    $pid = InputPackage::buildFromInput()->get('_pid', $this->treeRootPid);
                } else {
                    $pid = $item->{$this->repository->getTreePidColumn()};
                }
                $this->repository()->setArgument('treePid', $pid);
                $ancestors = $this->repository->getTreeAncestorItems();
                $html = [];
                $html[] = '<span class="ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span> ' . L('Root');
                foreach ($ancestors as $ancestor) {
                    $html[] = '<span class="ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span> ' . htmlspecialchars($ancestor->{$this->repository->getTreeTitleColumn()});
                }
                $html[] = '<input type="hidden" name="' . $this->repository->getTreePidColumn() . '" value="' . htmlspecialchars($pid) . '" />';
                return AutoRenderedFieldValue::make(join('', $html));
            });
            $this->prependField($field);
        }
    }

    
    public function repository()
    {
        return $this->repository;
    }

    private function fieldValidateMessages($fields, $input)
    {
        $failedValidators = [];
        
        foreach ($fields as $field) {
            if (!$validator = $field->getValidator($input)) {
                continue;
            }
            if ($validator instanceof Validator) {
                try {
                    if (!$validator->passes()) {
                        $failedValidators[] = $validator;
                    }
                } catch (\Exception $e) {
                    BizException::throws('Form.fieldValidateMessages.Error - ' . SerializeUtil::jsonEncode($validator->getRules()));
                }
            }
        }
        $msgs = [];
        foreach ($failedValidators as $validator) {
            foreach ($validator->messages()->getMessages() as $column => $messages) {
                $msgs[$column] = $messages;
            }
        }
        if (empty($msgs)) {
            return false;
        }
        return $msgs;
    }

    private function convertBizExceptionToResponse($exception)
    {
        $message = $exception->getMessage();
        $messageTemplates = [
            ['FieldTooLong:', 'Field %s Too Long'],
            ['FieldFormatError:', 'Field %s Format Error'],
        ];
        foreach ($messageTemplates as $m) {
            if (Str::startsWith($message, $m[0])) {
                list($_, $c) = explode(':', $message);
                $field = $this->getFieldByColumn($c);
                if ($field) {
                    return Response::jsonError(L($m[1], $field->label()));
                }
            }
        }
        return Response::jsonError($message);
    }

    private function validateFields($fields, $data)
    {
        $msgsList = [];
        if ($validationMessages = $this->fieldValidateMessages($fields, $data)) {
            $msgsList = array_merge($msgsList, $validationMessages);
        }
        foreach ($msgsList as $column => $msgs) {
            foreach ($msgs as $msg) {
                return Response::generateError($msg);
            }
        }
        return Response::generateSuccess();
    }


    
    private function removeReservedFields()
    {
        $reservedColumns = [
            $this->repository->getKeyName(),
            $this->repository->getCreatedAtColumn(),
            $this->repository->getUpdatedAtColumn(),
        ];
        $reject = function (AbstractField $field) use (&$reservedColumns) {
            return in_array($field->column(), $reservedColumns, true)
                && $field instanceof \ModStart\Field\Display;
        };
        $this->fields = $this->fields()->reject($reject);
    }

    public function hookCall($callback)
    {
        if ($callback instanceof Closure) {
            $ret = call_user_func($callback, $this);
            if (null !== $ret) {
                return $ret;
            }
        }
        return Response::generateSuccess();
    }

    public function isModeForm()
    {
        return $this->mode === FormMode::FORM;
    }

    public function isModeAdd()
    {
        return $this->mode === FormMode::ADD;
    }

    public function isModeEdit()
    {
        return $this->mode === FormMode::EDIT;
    }

    public function isModeDelete()
    {
        return $this->mode === FormMode::DELETE;
    }

    
    public function formRequest($callback, array $data = null)
    {
        $this->mode(FormMode::FORM);
        $this->build();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            ResultException::throwsIfFail($this->validateFields($this->addableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataForming = [];
            $this->removeReservedFields();
            foreach ($this->addableFields() as $field) {
                if ($field->isLayoutField()) {
                    continue;
                }
                $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                $value = $field->prepareInput($value, $this->dataSubmitted);
                $value = $field->serializeValue($value, $field);
                if ($field->hookValueSerialize()) {
                    $value = call_user_func($field->hookValueSerialize(), $value, $field);
                }
                $this->dataForming[$field->column()] = $value;
            }
            ResultException::throwsIfFail($this->hookCall($this->hookSaving));
            $ret = call_user_func($callback, $this);
            if (null !== $ret) {
                if (Response::isRaw($ret)) {
                    return $ret;
                }
                if (Response::isError($ret)) {
                    return Response::jsonFromGenerate($ret);
                }
            }
            ResultException::throwsIfFail($this->hookCall($this->hookSaved));
            ResultException::throwsIfFail($this->hookCall($this->hookChanged));
            if (null !== $ret) {
                return Response::jsonFromGenerate($ret);
            }
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            $res = null;
            if ($this->hookResponse()) {
                $res = call_user_func($this->hookResponse(), $this);
            }
            if (empty($res)) {
                return Response::jsonSuccess(L('Save Success'));
            }
            return $res;
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    
    public function add()
    {
        $this->mode(FormMode::ADD);
        $isCopy = false;
        if ($this->canCopy()) {
            $copyId = CRUDUtil::copyId();
            if ($copyId) {
                $this->itemId($copyId);
                $this->item($this->repository()->editing($this));
                $this->itemId(0);
                $isCopy = true;
            }
        }
        $this->build();
        if ($isCopy) {
            $this->fillFields();
        }
        return $this;
    }

    
    public function addRequest(array $data = null)
    {
        if (!$this->canAdd) return Response::pagePermissionDenied();
        $this->mode(FormMode::ADD);
        $this->build();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            ResultException::throwsIfFail($this->validateFields($this->addableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataAdding = [];
            $this->removeReservedFields();
            foreach ($this->addableFields() as $field) {
                if ($field->isLayoutField() || $field->isCustomField()) {
                    continue;
                }
                $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                $value = $field->prepareInput($value, $this->dataSubmitted);
                $value = $field->serializeValue($value, $field);
                if ($field->hookValueSerialize()) {
                    $value = call_user_func($field->hookValueSerialize(), $value, $field);
                }
                $this->dataAdding[$field->column()] = $value;
            }
                        $id = $this->repository->add($this);
            foreach ($this->addableFields() as $field) {
                if ($field->hookValueSaved()) {
                    call_user_func($field->hookValueSaved(), $id, $field);
                }
            }
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            $res = null;
            if ($this->hookResponse()) {
                $res = call_user_func($this->hookResponse(), $this);
            }
            if (empty($res)) {
                return Response::jsonSuccess(L('Add Success'));
            }
            return $res;
        } catch (BizException $e) {
            return $this->convertBizExceptionToResponse($e);
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    
    public function edit($id)
    {
        try {
            $this->mode(FormMode::EDIT);
            $this->itemId($id);
            $this->item($this->repository()->editing($this));
            BizException::throwsIfEmpty(L('Record Not Exists'), $this->item);
            $this->build();
            $this->fillFields();
            return $this;
        } catch (BizException $e) {
            return Response::sendError($e->getMessage());
        }
    }

    
    public function editRequest($id, array $data = null)
    {
        if (!$this->canEdit) return Response::pagePermissionDenied();
        $this->dataSubmitted = $data ? $data : Input::all();
        try {
            $this->edit($id);
            ResultException::throwsIfFail($this->validateFields($this->editableFields(), $this->dataSubmitted));
            ResultException::throwsIfFail($this->hookCall($this->hookSubmitted));
            $this->dataEditing = [];
            $this->removeReservedFields();
            $action = isset($this->dataSubmitted['_action']) ? $this->dataSubmitted['_action'] : null;
            if ('itemCellEdit' == $action) {
                $column = isset($this->dataSubmitted['column']) ? $this->dataSubmitted['column'] : null;
                $value = isset($this->dataSubmitted['value']) ? $this->dataSubmitted['value'] : null;
                if ($column) {
                    foreach ($this->editableFields() as $field) {
                        if ($field->isLayoutField() || $field->isCustomField()) {
                            continue;
                        }
                        if ($field->column() == $column) {
                            $value = $field->prepareInput($value, $this->dataSubmitted);
                            $value = $field->serializeValue($value, $field);
                            if ($field->hookValueSerialize()) {
                                $value = call_user_func($field->hookValueSerialize(), $value, $field);
                            }
                            $this->dataEditing[$field->column()] = $value;
                            break;
                        }
                    }
                }
                BizException::throwsIfEmpty('Data Error', $this->dataEditing);
            } else {
                foreach ($this->editableFields() as $field) {
                    if ($field->isLayoutField() || $field->isCustomField()) {
                        continue;
                    }
                    $value = isset($this->dataSubmitted[$field->column()]) ? $this->dataSubmitted[$field->column()] : null;
                    $value = $field->prepareInput($value, $this->dataSubmitted);
                    $value = $field->serializeValue($value, $field);
                    if ($field->hookValueSerialize()) {
                        $value = call_user_func($field->hookValueSerialize(), $value, $field);
                    }
                    $this->dataEditing[$field->column()] = $value;
                }
            }
            $this->repository()->edit($this);
            if ('itemCellEdit' == $action) {
                foreach ($this->editableFields() as $field) {
                    if ($field->column() == $column && $field->hookValueSaved()) {
                        call_user_func($field->hookValueSaved(), $this->itemId(), $field);
                    }
                }
            } else {
                foreach ($this->editableFields() as $field) {
                    if ($field->hookValueSaved()) {
                        call_user_func($field->hookValueSaved(), $this->itemId(), $field);
                    }
                }
            }
            if (!empty($this->dataSubmitted['_redirect'])) {
                return Response::json(0, null, null, $this->dataSubmitted['_redirect']);
            }
            $res = null;
            if ($this->hookResponse()) {
                $res = call_user_func($this->hookResponse(), $this);
            }
            if (empty($res)) {
                return Response::jsonSuccess(L('Edit Success'));
            }
            return $res;
        } catch (BizException $e) {
            return $this->convertBizExceptionToResponse($e);
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    
    public function deleteRequest($ids)
    {
        if (!$this->canDelete) return Response::pagePermissionDenied();
        $this->mode(FormMode::DELETE);
        $this->itemId($ids);
        $this->build();
        try {
            $data = $this->repository->deleting($this);
            $this->item($data);
            $this->itemId(collect($data)->map(function ($o) {
                return $o->{$this->repository()->getKeyName()};
            })->toArray());
            $result = $this->repository->delete($this, $data);
            $res = null;
            if ($this->hookResponse()) {
                $res = call_user_func($this->hookResponse(), $this);
            }
            if (empty($res)) {
                return Response::jsonSuccess(L('Delete Success'));
            }
            return $res;
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    public function sortRequest($ids)
    {
        if (!$this->canSort) return Response::pagePermissionDenied();
        $this->mode(FormMode::SORT);
        $this->itemId($ids);
        $input = InputPackage::buildFromInput();
        $this->repository->setArgument('direction', $input->getType('direction', SortDirection::class));
        $this->build();
        try {
            $result = $this->repository->sortEdit($this);
            ResultException::throwsIfFail($this->hookCall($this->hookChanged));
            return Response::jsonSuccess(L('Operate Success'));
        } catch (BizException $e) {
            return Response::jsonError($e->getMessage());
        } catch (ResultException $e) {
            return Response::jsonError($e->getMessage());
        }
    }

    
    public function render()
    {
        $data = [];
        switch ($this->mode) {
            case FormMode::FORM:
                $data['fields'] = $this->addableFields(true);
                break;
            case FormMode::ADD:
                if (!$this->canAdd) return Response::pagePermissionDenied();
                $data['fields'] = $this->addableFields(true);
                break;
            case FormMode::EDIT;
                if (!$this->canEdit) return Response::pagePermissionDenied();
                $data['fields'] = $this->editableFields(true);
                break;
            default:
                return Response::sendError('Form.render mode error : ' . $this->mode);
        }
        $data = array_merge($this->fluentAttributeVariables(), $data);
        return view($this->view, $data)->render();
    }

    
    public function __call($method, $arguments)
    {
        switch ($method) {
            case 'enablePagination':
            case 'defaultOrder':
            case 'canShow':
            case 'canExport':
            case 'canImport':
            case 'canBatchDelete':
            case 'canMultiSelectItem':
            case 'addBlankPage':
            case 'editBlankPage':
            case 'disableCUD':
            case 'hookItemOperateRendering':
            case 'addDialogSize':
            case 'editDialogSize':
            case 'dialogSizeSmall':
            case 'gridFilter':
            case 'gridOperateAppend':
            case 'bodyAppend':
            case 'operateFixed':
            case 'defaultPageSize':
            case 'pageSizes':
            case 'canBatchSelect':
            case 'batchOperatePrepend':
            case 'gridToolbar';
            case 'pageJumpEnable';
            case 'textEdit':
            case 'gridTableTopView':
            case 'gridBeforeRequestScriptView':
            case 'gridRequestScript':
            case 'useSimple':
            case 'gridRowCols':
                return $this;
        }
        if ($this->isFluentAttribute($method)) {
            switch ($method) {
                case 'item':
                    if (isset($arguments[0]) && is_array($arguments[0])) {
                        $arguments[0] = RepositoryUtil::itemFromArray($arguments[0]);
                    }
                    break;
            }
            return $this->fluentAttribute($method, $arguments);
        }
        return FieldManager::call($this, $method, $arguments);
    }

    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
