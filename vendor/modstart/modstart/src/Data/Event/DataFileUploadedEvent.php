<?php


namespace ModStart\Data\Event;


use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EventUtil;


class DataFileUploadedEvent
{
    
    const OPT_IMAGE_COMPRESS_IGNORE = 'imageCompressIgnore';
    
    const OPT_IMAGE_WATERMARK_IGNORE = 'imageWatermarkIgnore';
    
    const OPT_PARAM = 'param';

    public $driver;
    public $category;
    public $path;
    public $opt;

    public static function fire($driver, $category, $path, $opt = [])
    {
        $event = new static();
        $event->driver = $driver;
        $event->category = $category;
        $event->path = $path;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        return ArrayUtil::getByDotKey($this->opt, $key, $defaultValue);
    }

    private static $param = [];

    
    public static function setParam($key, $value)
    {
        self::$param[$key] = $value;
    }

    
    public static function forgetParam($key)
    {
        unset(self::$param[$key]);
    }

    
    public static function getParam($key)
    {
        return isset(self::$param[$key]) ? self::$param[$key] : null;
    }

}
