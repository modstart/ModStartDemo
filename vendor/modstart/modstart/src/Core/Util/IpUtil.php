<?php

namespace ModStart\Core\Util;

use Symfony\Component\HttpFoundation\IpUtils;

class IpUtil
{
    
    public static function match4($ip, $ipRange)
    {
                $ipRange = str_replace('－', '-', $ipRange);
        if (strpos($ipRange, '-') !== false) {
            list($start, $end) = explode('-', $ipRange);
            return ip2long($ip) >= ip2long($start) && ip2long($ip) <= ip2long($end);
        }
                return IpUtils::checkIp4($ip, $ipRange);
    }
}
