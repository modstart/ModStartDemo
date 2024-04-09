<?php


namespace Module\Vendor\Provider\Recommend;

abstract class AbstractRecommendBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function providerName();

    
    abstract public function syncBatch($nextId, $param = []);


    public static function itemUpdate($bizId, $sceneId = 0, $tags = [], $param = [])
    {
        $biz = RecommendBiz::getByName(static::NAME);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemUpdate(
                $biz->name(),
                $bizId,
                $sceneId,
                $tags,
                $param
            );
        }
    }

    public static function itemDelete($bizId, $param = [])
    {
        $biz = RecommendBiz::getByName(static::NAME);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemDelete(
                $biz->name(),
                $bizId,
                $param
            );
        }
    }

    public static function itemTrash($param = [])
    {
        $biz = RecommendBiz::getByName(static::NAME);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemTrash($biz->name(), $param);
        }
    }

    public static function itemCount($param = [])
    {
        $biz = RecommendBiz::getByName(static::NAME);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemCount($biz->name(), $param);
        }
    }

    public static function itemFeedback($bizId, $userId, $type, $param = [])
    {
        $biz = RecommendBiz::getByName(static::NAME);
        if (!$biz) {
            return;
        }
        foreach (RecommendProvider::listAll() as $provider) {
            if ($biz->providerName() != $provider->name()) {
                continue;
            }
            $provider->itemFeedback(
                $biz->name(),
                $bizId,
                $userId,
                $type,
                $param
            );
        }
    }

    public static function itemFeedbackVisit($bizId, $userId, $param = [])
    {
        self::itemFeedback($bizId, $userId, RecommendUserFeedbackType::VISIT, $param);
    }
}
