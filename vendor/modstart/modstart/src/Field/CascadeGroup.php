<?php

namespace ModStart\Field;

class CascadeGroup extends AbstractField
{
    protected $isLayoutField = true;

    public static function getAssets()
    {
        return [
                    ];
    }

    
    public function render()
    {
        $column = $this->column();
        return <<<HTML
<div class="ub-field-cascade-group cascade-group-hide" id="$column">
HTML;
    }

    
    public function end()
    {
        $this->context->html($this->column() . '_end')->html('</div>')->plain();
    }
}
