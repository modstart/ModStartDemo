<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Facades\Agent;


class AgentUtil
{
    
    public static function getUserAgent()
    {
        static $userAgent = null;
        if (null === $userAgent) {
            $userAgent = Request::header('User-Agent');
        }
        return $userAgent;
    }

    
    public static function device()
    {
        if (self::isMobile()) {
            return 'mobile';
        }
        return 'pc';
    }

    
    public static function isWechat()
    {
        static $isWechat = null;
        if (null === $isWechat) {
            $isWechat = false;
            if (strpos(self::getUserAgent(), 'MicroMessenger') !== false) {
                $isWechat = true;
            }
        }
        return $isWechat;
    }

    
    public static function isWechatMobile()
    {
        return self::isWechat() && !self::isWechatPC();
    }

    
    public static function isWechatPC()
    {
        static $isWechatPC = null;
        if (null === $isWechatPC) {
            $isWechatPC = false;
            if (self::isWechat()) {
                $ua = self::getUserAgent();
                if (
                    strpos($ua, 'WindowsWechat') !== false
                    ||
                    strpos($ua, 'MacWechat') !== false
                ) {
                    $isWechatPC = true;
                }
            }
        }
        return $isWechatPC;
    }

    
    public static function isMobile()
    {
        return Agent::isPhone() && !self::isWechatPC();
    }

    
    public static function isPC()
    {
        return !self::isMobile();
    }

    private static $robots = [

        '/googlebot/i' => 'Google',
        '/baiduspider/i' => 'Baidu',
        '/360spider/i' => '360',
        '/sogou/i' => 'Sogou',
        '/bingbot/i' => 'Bing',
        '/bytespider/i' => 'TouTiao',

        '/crawler/i' => 'Other',
        '/spider/i' => 'Other',
                '/(?:^|[\\W])\\w*bot([\\W\\s]|$)/i' => 'Other',
        '/detector/i' => 'Other',

                '/(curl|python|java|node-fetch|http-client|msray-plus|guzzlehttp|wget|okhttp|scrapy|https?:\\/\\/)/i' => 'Other',

                '/(ows.eu|researchscan|github|LogStatistic|Dataprovider|facebook|YandexImages|Iframely|panscient|netcraft|yahoo|censys|Turnitin)/i' => 'Other',
    ];

    
    public static function detectRobot($userAgent = null)
    {
        if (null === $userAgent) {
            $userAgent = AgentUtil::getUserAgent();
        }
        foreach (self::$robots as $regex => $robot) {
            if (preg_match($regex, $userAgent)) {
                return $robot;
            }
        }
        return null;
    }

}
