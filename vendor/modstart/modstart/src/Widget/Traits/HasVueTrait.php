<?php

namespace ModStart\Widget\Traits;

use ModStart\ModStart;

trait HasVueTrait
{
    
    abstract public function script();

    
    abstract public function template();

    public function content()
    {
        ModStart::js([
            'asset/vendor/vue.js',
            'asset/vendor/element-ui/index.js',
        ]);
        ModStart::script(join('', [
            "Vue.use(ELEMENT, {size: 'mini', zIndex: 3000});",
            $this->script()
        ]));
        return $this->template();
    }
}
