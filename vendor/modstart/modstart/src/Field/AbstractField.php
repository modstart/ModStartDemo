<?php


namespace ModStart\Field;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\IdUtil;
use ModStart\Detail\Detail;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\ModStart;
use ModStart\Support\Concern\HasFluentAttribute;
use ModStart\Support\Manager\FieldManager;


class AbstractField implements Renderable
{
    use HasFluentAttribute;

    protected static $css = [];
    protected static $js = [];
    protected $script = '';

    
    protected $context;

    protected $id;
    
    protected $name;
    
    protected $column;
    
    protected $label;
    
    protected $value = null;
    
    protected $defaultValue = null;
    
    protected $fixedValue = null;
    
    protected $rules = [];
    protected $view = null;
    protected $variables = [];
    
    protected $item;

    protected $fluentAttributes = [
        'listable',
        'addable',
        'editable',
        'formShowOnly',
        'showable',
        'sortable',
        'renderMode',
        'defaultValue',
        'placeholder',
        'help',
        'tip',
        'width',
        'styleFormField',
        'readonly',
        'hookFormatValue',
        'hookValueUnserialize',
        'hookValueSerialize',
        'hookValueSaved',
        'hookRendering',
        'isLayoutField',
        'isCustomField',
        'gridFixed',
        'gridEditable',
    ];
    
    protected $listable = true;
    protected $addable = true;
    protected $editable = true;
    protected $formShowOnly = false;
    protected $showable = true;
    protected $sortable = false;
    protected $renderMode;
    protected $placeholder = null;
    protected $help = null;
    protected $tip = null;
    protected $styleFormField = null;
    protected $width = '';
    protected $readonly = false;
    
    protected $hookFormatValue;
    
    protected $hookValueUnserialize;
    
    protected $hookValueSerialize;
    
    protected $hookValueSaved;
    protected $hookRendering;
    
    protected $isLayoutField = false;
    
    protected $isCustomField = false;
    
    protected $gridFixed = null;
    
    protected $gridEditable = false;

    public static function getAssets()
    {
        return [
            'css' => static::$css,
            'js' => static::$js,
        ];
    }

    public function __construct($column, $arguments = [])
    {
        $this->id = IdUtil::generate('Field');
        $this->column = $column;
        if (isset($arguments[0])) {
            $this->label = $arguments[0];
        } else {
            $this->label = null;
        }
        $this->setup();
        FieldManager::uses(static::class);
    }

    protected function setup()
    {

    }

    public function postSetup()
    {

    }

    public function context($context = null)
    {
        if (null === $context) {
            return $this->context;
        }
        $this->context = $context;
        return $this->context;
    }

    public function rules($rule = null)
    {
        if (is_null($rule)) {
            return $this->rules;
        }
                if (is_array($rule)) {
            $rule = array_filter($rule);
            $this->rules = array_merge($this->rules, $rule);
        } else {
            if (!empty($rule)) {
                $this->rules[] = $rule;
            }
        }
                        return $this;
    }

    
    public function required()
    {
        return $this->rules('required');
    }

    
    public function ruleRegex($regex)
    {
        return $this->rules('regex:' . $regex);
    }

    
    public function ruleUrl()
    {
        return $this->ruleRegex('/^https?:\/\//');
    }

    
    public function ruleUnique($table, $field = null)
    {
        if (null === $field) {
            $field = $this->column();
        }
        return $this->rules('unique:' . $table . ',' . $field . ',' . CRUDUtil::id());
    }

    public function id()
    {
        return $this->id;
    }

    public function column()
    {
        return $this->column;
    }

    private function formatName($column)
    {
        if (is_string($column)) {
            $names = explode('.', $column);

            if (count($names) == 1) {
                return $names[0];
            }

            $name = array_shift($names);
            foreach ($names as $piece) {
                $name .= "[$piece]";
            }
            return $name;
        }
    }

    public function name($value = null)
    {
        if (null === $value) {
            if (empty($this->name)) {
                return $this->formatName($this->column);
            }
            return $this->name;
        }
        $this->name = $value;
        return $this;
    }

    public function label($value = null)
    {
        if (null === $value) {
            return $this->label;
        }
        $this->label = $value;
        return $this;
    }

    public function value($value = null)
    {
        if (null === $value) {
            return $this->value;
        }
        $this->value = $value;
        return $this;
    }

    public function fixedValue($fixedValue = null)
    {
        if (null === $fixedValue) {
            return $this->fixedValue;
        }
        $this->fixedValue = $fixedValue;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    
    public function item($item = null)
    {
        if (null === $item) {
            return $this->item;
        }
        $this->item = $item;
        return $this;
    }

    
    public function unserializeValue($value, AbstractField $field)
    {
        return $value;
    }

    
    public function serializeValue($value, $model)
    {
        return $value;
    }

    
    public function prepareInput($value, $dataSubmitted)
    {
        return $value;
    }

    
    public function fill($item)
    {
        if ($this->isLayoutField()) {
            return;
        }
        $this->item = $item;
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $value = array_get($item, $column);
                if ($this->hookValueUnserialize) {
                    $value = call_user_func($this->hookValueUnserialize, $value, $this);
                }
                $value = $this->unserializeValue($value, $this);
                if ($this->hookFormatValue) {
                    $value = call_user_func($this->hookFormatValue, $value, $this);
                }
                $this->value[$key] = $value;
            }
            return;
        }
        if (is_array($item)) {
            $value = array_get($item, $this->column);
        } else {
            if (str_contains($this->column, '.')) {
                $value = ModelUtil::traverse($item, $this->column);
            } else {
                $value = isset($item->{$this->column}) ? $item->{$this->column} : null;
            }
                    }
        if ($this->hookValueUnserialize) {
            $value = call_user_func($this->hookValueUnserialize, $value, $this);
        }
        $value = $this->unserializeValue($value, $this);
        if ($this->hookFormatValue) {
            $value = call_user_func($this->hookFormatValue, $value, $this);
        }
        $this->value = $value;
    }

    
    public function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
        return $this;
    }

    public function getVariable($key, $default = null)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }
        return $default;
    }

    
    public function getValidator(array $input)
    {
        $rules = $attributes = [];
        if (!$fieldRules = $this->rules()) {
            return false;
        }

        if (is_string($this->column)) {
            if (!array_has($input, $this->column)) {
                return false;
            }
            $rules[$this->column] = $fieldRules;
            $attributes[$this->column] = $this->label;
        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                if (!array_key_exists($column, $input)) {
                    continue;
                }
                $input[$column . $key] = array_get($input, $column);
                $rules[$column . $key] = $fieldRules;
                $attributes[$column . $key] = $this->label . "[$column]";
            }
        }
        return Validator::make($input, $rules, [], $attributes);
    }


    protected function variables()
    {
                $variables = array_merge($this->fluentAttributeVariables(), $this->variables, [
            'id' => $this->id,
            'name' => $this->name(),
            'value' => $this->value(),
            'fixedValue' => $this->fixedValue(),
            'label' => $this->label,
            'column' => $this->column,
            'placeholder' => $this->placeholder(),
            'rules' => $this->rules,
        ]);
                return $variables;
    }

    private function transformVariables($variables, $param)
    {
        switch ($this->renderMode()) {
            case FieldRenderMode::GRID:
                if ($variables['gridEditable'] instanceof \Closure) {
                    $variables['gridEditable'] = call_user_func_array($variables['gridEditable'], [
                        $this,
                        $param['item'],
                        $param['index'],
                    ]);
                }
                break;
        }
        return $variables;
    }

    public function view($mode = '', $name = null)
    {
        if (!empty($this->view)) {
            return $this->view . ($mode ? '-' . $mode : '');
        }
        if (null === $name) {
            $class = explode('\\', get_called_class());
            $name = lcfirst(end($class));
        }
        return 'modstart::core.field.' . $name . ($mode ? '-' . $mode : '');
    }

    public function render()
    {
        return $this->renderView($this, $this->item);
    }

    public function renderView(AbstractField $field, $item, $index = 0)
    {
        try {
            $column = $field->column();
            if ($this->hookRendering instanceof \Closure) {
                $ret = call_user_func($this->hookRendering, $this, $item, $index);
                if (null !== $ret) {
                    if ($ret instanceof AutoRenderedFieldValue) {
                        return view('modstart::core.field.autoRenderedField-' . $this->renderMode, [
                            'label' => $this->label,
                            'tip' => $this->tip,
                            'help' => $this->help,
                            'value' => $ret->getValue(),
                            'rules' => $this->rules,
                        ])->render();
                    }
                    return $ret;
                }
            }
            ModStart::script($this->script);
            $variables = $this->variables();
            switch ($this->renderMode) {
                case FieldRenderMode::FORM:
                    return View::make($this->view(), $variables)->render();
                case FieldRenderMode::DETAIL:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                        return View::make($view, $variables)->render();
                    }
                    return View::make($this->view($this->renderMode, 'text'), $variables)->render();
                case FieldRenderMode::GRID:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                                                $variables = $this->transformVariables($variables, [
                            'item' => $item,
                            'index' => $index,
                        ]);
                        return View::make($view, array_merge([
                            'item' => $item,
                            '_index' => $index,
                        ], $variables))->render();
                    }
                    if (is_array($item->{$column})) {
                        return join(', ', $item->{$column});
                    }
                    if (str_contains($column, '.')) {
                        $value = (string)ModelUtil::traverse($item, $column);
                                            } else {
                        $value = (string)$item->{$column};
                    }
                    return htmlspecialchars($value);
            }
        } catch (\Throwable $e) {
            Log::error('FieldRenderModeError - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return new \Exception('FieldRenderModeError - ' . $e->getMessage());
        }
        throw new \Exception('FieldRenderModeNotExist');
    }

    public function __call($method, $arguments)
    {
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        throw new \Exception('AbstractField __call error : ' . $method);
    }

}
