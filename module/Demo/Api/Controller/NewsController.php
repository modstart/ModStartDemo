<?php


namespace Module\Demo\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use Module\News\Util\NewsUtil;


class NewsController extends Controller
{
    
    public function get()
    {
        $news = ModelUtil::get('demo_news', CRUDUtil::id());
        BizException::throwsIfEmpty('新闻不存在', $news);
        return Response::generateSuccessData([
            'news' => $news,
        ]);
    }

    
    public function paginate()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize();
        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];
        $categoryId = $input->getInteger('categoryId');
        if ($categoryId) {
            $option['where']['categoryId'] = $categoryId;
        }
        $paginateData = ModelUtil::paginate('demo_news', $page, $pageSize, $option);
        return Response::generateSuccessData([
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $paginateData['total'],
            'records' => $paginateData['records'],
        ]);
    }
}
