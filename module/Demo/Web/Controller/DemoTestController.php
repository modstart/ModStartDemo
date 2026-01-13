<?php


namespace Module\Demo\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;

class DemoTestController extends ModuleBaseController
{
    
    private $api;

    
    public function __construct(\Module\Demo\Api\Controller\DemoTestController $api)
    {
        $this->api = $api;
    }

    public function index()
    {
        $viewData = Response::tryGetData($this->api->paginate());
        $viewData['pageHtml'] = PageHtmlUtil::render($viewData['total'], $viewData['pageSize'], $viewData['page'], '?' . Request::mergeQueries(['page' => ['{page}']]));
        return $this->view('demo.test', $viewData);
    }

    public function show($id)
    {
        InputPackage::mergeToInput('id', $id);
        $viewData = Response::tryGetData($this->api->get());
        return $this->view('demo.testShow', $viewData);
    }
}
