<?php


namespace Module\Demo\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use Module\Demo\Util\DemoNewsCategoryUtil;


class DemoNewsCategoryController extends Controller
{
    
    public function all()
    {
        $data = [];
        $data['records'] = DemoNewsCategoryUtil::all();
        $data['tree'] = DemoNewsCategoryUtil::tree();
        return Response::generateSuccessData($data);
    }
}
