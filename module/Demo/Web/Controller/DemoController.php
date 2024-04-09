<?php


namespace Module\Demo\Web\Controller;


use ModStart\Module\ModuleBaseController;

class DemoController extends ModuleBaseController
{
    public function index()
    {
        return $this->view('demo.index');
    }
}
