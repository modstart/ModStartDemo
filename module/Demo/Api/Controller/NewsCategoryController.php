<?php


namespace Module\Demo\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;


class NewsCategoryController extends Controller
{
    
    public function all()
    {
        return Response::generateSuccessData(ModelUtil::all('demo_news_category'));
    }
}
