<?php


namespace Module\Demo\Util;


use ModStart\Core\Dao\ModelUtil;
use Module\Demo\Model\DemoNews;
use Module\Vendor\Util\CacheUtil;

class DemoNewsUtil
{
    public static function clearCache()
    {
        CacheUtil::forget('Demo:News');
    }

    public static function get($id)
    {
        return ModelUtil::get(DemoNews::class, $id);
    }

    public static function all()
    {
        return CacheUtil::remember('Demo:News', 60, function () {
            return ModelUtil::all(DemoNews::class);
        });
    }
}
