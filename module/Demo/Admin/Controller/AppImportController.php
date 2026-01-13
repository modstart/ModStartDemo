<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class AppImportController extends Controller
{
    use DemoPreviewTrait;

    public function index()
    {
        $this->setupDemoPreview('复杂数据导入示例，前端传入复杂数据结构，后台批量处理');

        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInput();
            $data = $input->getJson('importData');
            BizException::throwsIfEmpty('导入数据不能为空', $data);
            $results = [];
            foreach ($data as $dataItem) {
                                $ret = Response::generateSuccessData([
                    'dataItem' => $dataItem,
                ]);
                $results[] = $ret;
            }
            return Response::generateSuccessData([
                'results' => $results,
            ]);
        }
        return view('module::Demo.View.admin.app.import', [
            'pageTitle' => '复杂数据导入',
        ]);
    }
}
