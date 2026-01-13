<?php


namespace Module\Demo\Util;


use ModStart\Core\Dao\ModelUtil;
use Module\Demo\Model\DemoTest;
use Module\Vendor\Util\CacheUtil;

class DemoTestUtil
{
    public static function clearCache()
    {
        CacheUtil::forget('Demo:Test');
    }

    public static function get($id)
    {
        return ModelUtil::get(DemoTest::class, $id);
    }

    public static function all()
    {
        return CacheUtil::remember('Demo:Test', 60, function () {
            return ModelUtil::all(DemoTest::class);
        });
    }
}
