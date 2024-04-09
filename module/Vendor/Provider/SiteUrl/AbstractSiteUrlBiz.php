<?php


namespace Module\Vendor\Provider\SiteUrl;


abstract class AbstractSiteUrlBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function urlBuildBatch($nextId, $param = []);
                                                                
    public static function update($url, $title = '', $param = [])
    {
        SiteUrlProvider::updateBiz(static::NAME, $url, $title, $param);
    }
}
