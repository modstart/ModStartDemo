<?php

namespace ModStart\Field\Concern;

use Illuminate\Support\Arr;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\Checkbox;
use ModStart\Field\Radio;
use ModStart\Field\Select;
use ModStart\Field\SwitchField;
use ModStart\Field\Type;
use ModStart\Form\Form;
use ModStart\ModStart;


trait CanCascadeFields
{
    
    protected $whenHelps = [];

    
    protected $conditions = [];

    
    protected $cascadeGroups = [];

    
    protected $cascadeParam = [];

    
    public function when($operator, $value, $closure = null, $param = [])
    {
        if (func_num_args() == 2) {
            $closure = $value;
            $value = $operator;
            $operator = $this->getDefaultOperator();
        }
        $this->cascadeParam = $param;
        $this->formatValues($operator, $value);
        $this->addDependents($operator, $value, $closure);
        return $this;
    }

    public function whenHelps($helps)
    {
        $this->whenHelps = $helps;
        return $this;
    }

    protected function getDefaultOperator()
    {
        if ($this instanceof Checkbox) {
            return 'in';
        }
        return '=';
    }

    
    protected function formatValues($operator, &$value)
    {
        if (in_array($operator, ['in', 'notIn'])) {
            $value = SerializeUtil::jsonEncode($value);
        }
        if (is_array($value)) {
            $value = array_map('strval', $value);
        } else {
            if (!empty($this->cascadeParam['type'])) {
                switch ($this->cascadeParam['type']) {
                    case 'boolean':
                        $value = !!$value;
                        break;
                    default:
                        $value = strval($value);
                        break;
                }
            } else {
                $value = strval($value);
            }
        }
    }

    
    protected function addDependents($operator, $value, \Closure $closure)
    {
        $index = count($this->conditions);
        $this->conditions[] = [
            'operator' => $operator,
            'value' => $value,
            'closure' => $closure,
            'index' => $index,
        ];
        $this->context->cascadeGroup($closure, [
            'id' => $this->id(),
            'index' => $index,
        ]);
    }

    
    protected function addCascadeScript()
    {
        if (empty($this->conditions) && empty($this->whenHelps)) {
            return;
        }
        $cascadeGroups = collect($this->conditions)->map(function ($condition) use (&$index) {
            return ArrayUtil::keepKeys($condition, ['operator', 'value', 'index']);
        })->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $whenHelps = SerializeUtil::jsonEncode($this->whenHelps);
        $valueConverter = "function(v){ return v; }";
        if (!empty($this->cascadeParam['type'])) {
            switch ($this->cascadeParam['type']) {
                case 'boolean':
                    $valueConverter = "function(v){ return !!v; }";
                    break;
            }
        }
        $id = $this->id();
        $script = <<<JS
(function () {
    var valueConverter = $valueConverter;
    var operatorTable = {
       'in': function(a, b) {
           b = JSON.parse(b); a = String(a);
           for(var i=0;i<b.length;i++){
               b[i] = String(b[i]);
           }
           return b.indexOf(a)>=0;
       },
       'includes': function(a, b){
           if(!$.isArray(a)){
               a = [a];
           }
           return a.indexOf(b)>=0;
       },
       '=': function(a, b) {
           if ($.isArray(a) && $.isArray(b)) {
               return $(a).not(b).length === 0 && $(b).not(a).length === 0;
           }
           a = String(a); b = String(b);
           var ab = [a,b].sort().join('|');
           if(ab==='1|true'||ab==='0|false'){
               return true;
           }
           return a === b;
       },
       '>': function(a, b) {
           return a > b;
       },
       '<': function(a, b) {
           return a < b;
       },
       '>=': function(a, b) { return a >= b; },
       '<=': function(a, b) { return a <= b; },
       '!=': function(a, b) {
            return ! operatorTable['='](a, b);
       }
   };
   var compare = function (a, b, o) {
       if(!(o in operatorTable)){
           console.error('unsupported operator ',o)
       }
       if (! $.isArray(b) ) return operatorTable[o](a, b);
       if (o === '!=') {
           for (var i in b) {
               if (! operatorTable[o](a, b[i])) return false;
           }
           return true;
       }
       for (var i in b) {
           if (operatorTable[o](a, b[i])) return true;
       }
       return false;
   };
   var cascadeGroups = $cascadeGroups;
   var whenHelps = $whenHelps;
   var cascadeChange = function(value){
       value = valueConverter(value);
       var f = $('#{$this->id()}');
       var helps = [];
       Object.keys(whenHelps).forEach(function(k){
          if( compare(value, k, 'includes') ){
              helps.push(whenHelps[k]);
          }
       });
       var helpDom = f.find('.field > .when-help');
       if(!helpDom.length){
          helpDom = $('<div class="help when-help"></div>');
          f.find('.field').append(helpDom);
       }
       helpDom.html(helps.join('<br />'));
       cascadeGroups.forEach(function (group) {
           var groupDom = $('#{$this->id()}_group_' + group.index);
           groupDom.addClass('cascade-group-hide');
           groupDom.find('input,textarea,select').prop('disabled',true)
       });
       cascadeGroups.forEach(function (group) {
           var groupDom = $('#{$this->id()}_group_' + group.index);
           var pass = compare(value, group.value, group.operator);
           if (pass) {
               groupDom.removeClass('cascade-group-hide');
               groupDom.find('input,textarea,select').prop('disabled',false);
           }
        });
   };
   {$this->getFieldNormalizedScript()}
})();
JS;
        ModStart::script($script);
    }

    
    protected function getFieldNormalizedScript()
    {
        if ($this->context instanceof Form) {
            switch (get_class($this)) {
                case Select::class:
                case Type::class:
                    return <<<JS
$('#{$this->id()} select').on('change', function (e) {
    cascadeChange($(this).val());
}).trigger('change');
JS;
                case Radio::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    cascadeChange($('#{$this->id()} [type=radio]:checked').val());
}).trigger('click');
JS;
                case Checkbox::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    var value = $('#{$this->id()} [type=checkbox]:checked').map(function(){
      return $(this).val();
    }).get();
    cascadeChange(value);
}).trigger('click');
JS;
                case SwitchField::class:
                    return <<<JS
$('#{$this->id()}').on('click', function (e) {
    cascadeChange($('#{$this->id()} [type=checkbox]').is(':checked')?1:0);
}).trigger('click');
JS;
                default:
                    throw new \InvalidArgumentException('Invalid form field type');
            }
        } else {
            $value = SerializeUtil::jsonEncode($this->value);
            switch (get_class($this)) {
                case Select::class:
                case Type::class:
                    return <<<JS
cascadeChange($value);
JS;
                case Radio::class:
                    return <<<JS
cascadeChange($value);
JS;
                case Checkbox::class:
                    return <<<JS
cascadeChange($value);
JS;
                case SwitchField::class:
                    return <<<JS
cascadeChange($value);
JS;

                default:
                    throw new \InvalidArgumentException('Invalid form field type');
            }
        }
    }
}
