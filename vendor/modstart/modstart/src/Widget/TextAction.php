<?php


namespace ModStart\Widget;


use ModStart\ModStart;


class TextAction extends AbstractWidget
{
    public static function getAssets()
    {
        return [
            'style' => '.ub-text-action{display:inline-block;margin-right:0.5rem;}',
        ];
    }

    public static function __callStatic($name, $arguments)
    {
        $methods = ['primary', 'muted', 'warning', 'danger', 'success',];
        if (in_array($name, $methods)) {
            $ins = new static();
            $ins->type($name);
            $ins->text($arguments[0]);
            $ins->attr(empty($arguments[1]) ? '' : $arguments[1]);
            return $ins->render();
        }
        throw new \Exception('TextAction error ' . join(',', $methods) . ' ');
    }

    public function render()
    {
        if ($this->disabled) {
            return '<a href="javascript:;" class="ub-text-action ub-text-muted">' . $this->text . '</span>';
        } else {
            return '<a href="javascript:;" class="ub-text-action ub-text-' . ($this->type == 'primary' ? '' : $this->type) . '" ' . $this->attr . '>' . $this->text . '</span>';
        }
    }
}
