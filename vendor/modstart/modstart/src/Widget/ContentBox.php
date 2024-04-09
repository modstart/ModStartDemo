<?php

namespace ModStart\Widget;

use ModStart\Core\Util\RenderUtil;


class ContentBox extends AbstractWidget
{
    
    protected $view = 'modstart::widget.contentBox';

    
    protected $classList = '';

    
    protected $content = '';


    public static function make($content, $classList = 'margin-bottom')
    {
        $ins = new static();
        $ins->content($content);
        $ins->classList($classList);
        return $ins;
    }

    public static function breadcrumb($items, $classList = 'margin-bottom')
    {
        $ins = new static();
        $content = RenderUtil::view('modstart::widget.breadcrumb', ['items' => $items]);
        $ins->content($content);
        $ins->classList($classList);
        return $ins;
    }

    
    public function content($content)
    {
        $this->content = $content;
        return $this;
    }

    
    public function classList($classList)
    {
        $this->classList = $classList;
        return $this;
    }

    
    public function variables()
    {
        return [
            'classList' => $this->classList,
            'content' => $this->toString($this->content),
        ];
    }
}
