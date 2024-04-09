<?php

namespace ModStart\Core\Util;



class RandomUtil
{
    
    public static function number($length)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }


    
    public static function string($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function readableString($length)
    {
        $pool = '2345678abcdefghijkmnoprstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function lowerReadableString($length)
    {
        return strtolower(self::readableString($length));
    }

    
    public static function upperReadableString($length)
    {
        return strtoupper(self::readableString($length));
    }

    
    public static function hexString($length)
    {
        $pool = '0123456789abcdef';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function lowerString($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function lowerChar($length)
    {
        $pool = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function upperChar($length)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function upperString($length)
    {
        $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    
    public static function uuid()
    {
        return date('Ymd')
            . '-'
            . date('Hi')
            . '-'
            . date('s')
            . self::hexString(2)
            . '-'
            . self::hexString(4)
            . '-'
            . self::hexString(12);
    }

    
    public static function percent($value)
    {
        return rand(0, 99) < $value;
    }

    public static function dateCollection($length = 10)
    {
        $list = [];
        $t = time();
        for ($i = $t - $length * TimeUtil::MINUTE_PERIOD_DAY; $i < $t; $i += TimeUtil::MINUTE_PERIOD_DAY) {
            $list[] = date('Y-m-d', $i);
        }
        return $list;
    }

    public static function numberCollection($length = 10, $min = 10, $max = 100)
    {
        $list = [];
        for ($i = 0; $i < $length; $i++) {
            $list[] = rand($min, $max);
        }
        return $list;
    }

}
