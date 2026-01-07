<?php


namespace Module\Vendor\Provider\Captcha;

use ModStart\Core\Exception\BizException;


class CaptchaProvider
{
    
    private static $instances = [
        DefaultCaptchaProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    
    public static function all()
    {
        foreach (self::$instances as $k => $v) {
            if ($v instanceof \Closure) {
                self::$instances[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$instances[$k] = app($v);
            }
        }
        return self::$instances;
    }

    public static function nameTitleMap($empty = false)
    {
        $map = array_build(self::all(), function ($k, $v) {
            
            return [
                $v->name(),
                $v->title()
            ];
        });
        if ($empty) {
            $map = array_merge([
                '' => L('Empty')
            ], $map);
        }
        return $map;
    }

    public static function nameTitleMapWithNone()
    {
        $value = [];
        $value[''] = L('Empty');
        foreach (self::nameTitleMap() as $k => $v) {
            $value[$k] = $v;
        }
        return $value;
    }

    
    public static function get($name)
    {
        foreach (self::all() as $item) {
            
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    
    public static function hasProvider()
    {
        $provider = app()->config->get('CaptchaProvider');
        return !empty($provider);
    }
}
