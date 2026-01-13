<?php


namespace Module\Demo\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use Module\Demo\Util\DemoTestCategoryUtil;


class DemoTestCategoryController extends Controller
{
    
    public function all()
    {
        $data = [];
        $data['records'] = DemoTestCategoryUtil::all();
        $data['tree'] = DemoTestCategoryUtil::tree();
        return Response::generateSuccessData($data);
    }
}
