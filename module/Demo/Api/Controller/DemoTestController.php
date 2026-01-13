<?php


namespace Module\Demo\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use Module\Demo\Model\DemoTest;


class DemoTestController extends Controller
{
    
    public function get()
    {
        $record = ModelUtil::get(DemoTest::class, CRUDUtil::id());
        BizException::throwsIfEmpty('记录不存在', $record);
        return Response::generateSuccessData([
            'record' => $record,
        ]);
    }

    
    public function paginate()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize();
        $query = DemoTest::query();
        $categoryId = $input->getInteger('categoryId');
        if ($categoryId) {
            $query = $query->where('categoryId', $categoryId);
        }
        $query = $query->orderBy('id', 'desc');
        $resultData = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
        $records = $resultData['data'];
        $total = $resultData['total'];
        return Response::generateSuccessData([
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'records' => $records,
        ]);
    }
}
