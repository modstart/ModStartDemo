<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Field\Type\CustomFieldType;


class CustomField extends AbstractField
{
    protected $listable = false;

    protected function setup()
    {
        $this->addVariables([
        ]);
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        if (!isset($value['type'])) {
            $value['type'] = '';
        }
        if (!isset($value['title'])) {
            $value['title'] = '';
        }
        if (!isset($value['data'])) {
            $value['data'] = [];
        }
        if (!isset($value['data']['option'])) {
            $value['data']['option'] = [];
        }
        return $value;
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public static function getDefaultValueObject($fields)
    {
        $value = [];
        foreach ($fields as $f) {
            if (empty($f)) {
                continue;
            }
            $defaultValue = null;
            switch ($f['type']) {
                case CustomFieldType::TYPE_FILES:
                    $defaultValue = [];
                    break;
            }
            $value[$f['_name']] = $defaultValue;
        }
        return $value;
    }

    public static function fetchInputOrFail($fields, InputPackage $input, $param = [])
    {
        if (!isset($param['tipPrefix'])) {
            $param['tipPrefix'] = '';
        }
        $data = [];
        foreach ($fields as $f) {
            if (empty($f)) {
                continue;
            }
            switch ($f['type']) {
                case CustomFieldType::TYPE_TEXT:
                case CustomFieldType::TYPE_RADIO:
                    $data[$f['_name']] = $input->getTrimString($f['_name']);
                    break;
                case CustomFieldType::TYPE_FILE:
                    $data[$f['_name']] = $input->getDataUploadedPath($f['_name']);
                    break;
                case CustomFieldType::TYPE_FILES:
                    $data[$f['_name']] = $input->getDataUploadedPathArray($f['_name']);
                    break;
                default:
                    BizException::throws($param['tipPrefix'] . "不支持的字段类型: {$f['type']}");
            }
        }
        return $data;
    }

    public static function fetchValueObject($fields, $valueObject, $param = [])
    {
        $valueObjectForField = [];
        foreach ($fields as $f) {
            if (empty($f)) {
                continue;
            }
            $valueObjectForField[$f['_name']] = $valueObject[$f['_name']];
            switch ($f['type']) {
                case CustomFieldType::TYPE_FILES:
                    $valueObjectForField[$f['_name']] = @json_decode($valueObjectForField[$f['_name']], true);
                    if (empty($valueObjectForField[$f['_name']])) {
                        $valueObjectForField[$f['_name']] = [];
                    }
                    break;
            }
        }
        return $valueObjectForField;
    }

    public static function fetchedValueToString($field, $value, $param = [])
    {
        switch ($field['type']) {
            case CustomFieldType::TYPE_TEXT:
            case CustomFieldType::TYPE_RADIO:
            case CustomFieldType::TYPE_FILE:
                return $value;
            case CustomFieldType::TYPE_FILES:
                return join(',', $value);
            default:
                BizException::throws($param['tipPrefix'] . "不支持的字段类型: {$field['type']}");
        }
        return null;
    }

    public static function renderAllDetailTableTr($fields, $valueObject, $param = [])
    {

        return View::make('modstart::core.field.customField.detailTableTr', [
            'fields' => $fields,
            'value' => $valueObject,
            'param' => $param,
        ])->render();
    }


    
    public static function buildRecordFieldsValues($keyRecord, $valueRecord, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        self::buildFieldsData($keyRecord, $prefix, $fieldCount);
        $pairs = [];
        foreach ($keyRecord['_' . $prefix] as $f) {
            if (empty($f)) {
                continue;
            }
            $value = self::prepareDetail($f, $valueRecord[$f['_name']]);
            $pairs[] = [
                'name' => $f['_name'],
                'value' => $value,
                'field' => $f,
                'record' => $valueRecord,
            ];
        }
        return $pairs;
    }

    
    public static function hasFields($data, $prefix = 'fieldCustom', $fieldCount = 5)
    {
        for ($i = 1; $i <= $fieldCount; $i++) {
            if (empty($data[$prefix . $i])) {
                continue;
            }
            $field = $data[$prefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                continue;
            }
            return true;
        }
        return false;
    }

    
    public static function buildFieldsData(&$data, $fieldPrefix = 'fieldCustom', $fieldCount = 5)
    {
        if (empty($data)) {
            return;
        }
        $fieldModules = [];
        for ($i = 1; $i <= $fieldCount; $i++) {
            $field = $data[$fieldPrefix . $i];
            if (is_string($field)) {
                $field = @json_decode($field, true);
            }
            if (empty($field['type']) || empty($field['title'])) {
                $field = null;
            } else {
                $field['_name'] = $fieldPrefix . $i;
            }
            $fieldModules[] = $field;
        }
        $data['_' . $fieldPrefix] = $fieldModules;
    }

    
    public static function prepareInputOrFail($field, $fieldName, InputPackage $input)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!CustomFieldType::isValid($field['type'])) {
            return '';
        }
        switch ($field['type']) {
            case 'Text':
            case 'Radio':
                return $input->getTrimString($fieldName);
            case 'File':
                return $input->getFilePath($fieldName);
            case 'Files':
                $data = $input->getJsonFilesPath($fieldName);
                return SerializeUtil::jsonEncode($data);
        }
        BizException::throws('未知的自定义字段类型:' . SerializeUtil::jsonEncode($field));
    }

    
    public static function prepareDetail($field, $value)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!CustomFieldType::isValid($field['type'])) {
            return '';
        }
        switch ($field['type']) {
            case 'Text':
            case 'Radio':
            case 'File':
                return $value;
            case 'Files':
                if (!is_array($value)) {
                    $value = @json_decode($value, true);
                    if (empty($value) || !is_array($value)) {
                        $value = [];
                    }
                }
                return $value;
        }
        return null;
    }


    public static function renderAllFormFieldVue($fields, $param = [])
    {
        return View::make('modstart::core.field.customField.formFieldVue', [
            'fields' => $fields,
            'param' => $param,
        ])->render();
    }

    
    public static function renderForm($field, $fieldName, $value, $param = [])
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!CustomFieldType::isValid($field['type'])) {
            return '';
        }
        $value = self::prepareDetail($field, $value);
        return View::make('modstart::core.field.customField.form.' . $field['type'], [
            'fieldName' => $fieldName,
            'field' => $field,
            'value' => $value,
            'param' => $param,
        ])->render();
    }

    
    public static function renderDetail($field, $value)
    {
        if (empty($field['type']) || empty($field['title'])) {
            return '';
        }
        if (!CustomFieldType::isValid($field['type'])) {
            return '';
        }
        $value = self::prepareDetail($field, $value);
        return View::make('modstart::core.field.customField.detail.' . $field['type'], [
            'field' => $field,
            'value' => $value,
        ])->render();
    }

}
