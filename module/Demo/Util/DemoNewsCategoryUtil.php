<?php

namespace Module\Demo\Util;

use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;
use Module\Demo\Model\DemoNewsCategory;
use Module\Vendor\Util\CacheUtil;

class DemoNewsCategoryUtil
{
    public static function clearCache()
    {
        CacheUtil::forget('Demo:NewsCategories');
    }

    public static function allIds()
    {
        return ModelUtil::values(DemoNewsCategory::class, 'id');
    }

    public static function all()
    {
        return CacheUtil::rememberForever('Demo:NewsCategories', function () {
            $records = ModelUtil::all(DemoNewsCategory::class, [], [
                'id',
                'pid',
                'title',
                'sort',
                'cover',
            ], ['sort', 'asc']);
            AssetsUtil::recordsFixFullOrDefault($records, 'cover', 'vendor/Problem/image/catBgCover.jpg');
            return $records;
        });
    }

    public static function allListIndent()
    {
        $tree = self::tree();
        return TreeUtil::treeToListWithIndent($tree);
    }

    public static function allChildren($pid)
    {
        $all = self::all();
        $children = [];
        foreach ($all as $item) {
            if ($item['pid'] == $pid) {
                $children[] = $item;
            }
        }
        return $children;
    }

    public static function tree()
    {
        $records = self::all();
        $tree = TreeUtil::nodesToTree($records);
        return $tree;
    }

    public static function chain($id)
    {
        $records = self::all();
        return TreeUtil::nodesChain($records, $id);
    }

    public static function firstId()
    {
        foreach (self::all() as $item) {
            return $item['id'];
        }
        return 0;
    }

    public static function get($id)
    {
        foreach (self::all() as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }
}
