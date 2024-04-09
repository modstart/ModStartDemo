<?php

namespace ModStart\Widget;


class Nav extends AbstractWidget
{
    
    protected $view = 'modstart::widget.nav';

    
    protected $navs = '';

    
    protected $classList = '';

    
    public function __construct($navs, $classList = '')
    {
        parent::__construct();
        $this->navs = $navs;
        $this->classList = $classList;
    }

    public static function make($navs, $classList = '')
    {
        return new static($navs, $classList);
    }

    public function navs($navs)
    {
        $this->navs = $navs;
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
            'navs' => $this->navs,
            'classList' => $this->classList,
        ];
    }
}
