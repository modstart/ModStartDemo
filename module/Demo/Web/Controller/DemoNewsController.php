<?php


namespace Module\Demo\Web\Controller;

use ModStart\Module\ModuleBaseController;
use Module\Demo\Util\DemoNewsUtil;

class DemoNewsController extends ModuleBaseController
{
    public function show($id)
    {
        return $this->view('demo.news.show', [
            'record' => DemoNewsUtil::get($id)
        ]);
    }
}
