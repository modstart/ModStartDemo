<?php


namespace Module\Vendor\Provider\Recommend;

abstract class AbstractRecommendProvider
{
    abstract public function name();

    abstract public function title();

    
    abstract public function itemUpdate($biz, $bizId, $sceneId = 0, $tags = [], $param = []);

    
    abstract public function itemDelete($biz, $bizId, $param = []);


    
    abstract public function itemTrash($biz, $param = []);


    
    abstract public function itemCount($biz, $param = []);

    
    abstract public function itemFeedback($biz, $bizId, $userId, $type, $param = []);

    
    abstract public function randomItem($biz, $userId, $limit = 1, $sceneIds = [], $tags = [], $exceptBizIds = [], $param = []);

}
