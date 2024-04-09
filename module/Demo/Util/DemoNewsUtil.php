<?php


namespace Module\Demo\Util;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;

class DemoNewsUtil
{
    public static function clearCache()
    {
        Cache::forget('DemoNewsCategories');
        Cache::forget('DemoNews');
    }

    public static function get($id)
    {
        return ModelUtil::get('demo_news', $id);
    }

    public static function all()
    {
        return Cache::remember('DemoNews', 60, function () {
            return ModelUtil::all('demo_news');
        });
    }

    public static function categories()
    {
        return Cache::remember('DemoNewsCategories', 60, function () {
            return TreeUtil::modelToTree('demo_news_category', ['title' => 'title']);
        });
    }
}
