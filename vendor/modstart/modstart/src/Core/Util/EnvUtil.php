<?php

namespace ModStart\Core\Util;

use ModStart\Data\FileManager;

class EnvUtil
{
    public static function parseContent($content)
    {
        $all = [];
        if (empty($content)) {
            return $all;
        }
        foreach (explode("\n", $content) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if (substr($line, 0, 1) === '#') {
                continue;
            }
            $pcs = explode('=', $line);
            $k = trim($pcs[0]);
            array_shift($pcs);
            $v = trim(join('=', $pcs));
            switch (strtolower($v)) {
                case 'true':
                case '(true)':
                    $v = true;
                    break;
                case 'false':
                case '(false)':
                    $v = false;
                    break;
                case 'empty':
                case '(empty)':
                    $v = '';
                    break;
                case 'null':
                case '(null)':
                    $v = null;
                    break;
            }
            $all[$k] = $v;
        }
        return $all;
    }

    public static function parse($file)
    {
        $all = [];
        if (!file_exists($file)) {
            return $all;
        }
        return self::parseContent(file_get_contents($file));
    }

    public static function all($file = null)
    {
        global $__msConfig;
        if (!empty($__msConfig)) {
            return $__msConfig;
        }
        if (null === $file) {
            $file = base_path('.env');
        }
        return self::parse($file);
    }

    public static function env($key)
    {
        switch ($key) {
            case 'uploadMaxSize':
                                if (FileManager::$slowDebug) {
                    return 100;
                }
                $upload_max_filesize = @ini_get('upload_max_filesize');
                if (empty($upload_max_filesize)) {
                    return 0;
                }
                $post_max_size = @ini_get('post_max_size');
                if (empty($post_max_size)) {
                    return 0;
                }
                $upload_max_filesize = FileUtil::formattedSizeToBytes($upload_max_filesize);
                $post_max_size = FileUtil::formattedSizeToBytes($post_max_size);
                $size = min($upload_max_filesize, $post_max_size);
                                $size -= 10 * 1024;
                                return min(max($size, 100 * 1024), 2 * 1024 * 1024);
        }
        return null;
    }

    public static function iniFileConfig($key)
    {
        return @ini_get($key);
    }

    
    public static function securityKey()
    {
        static $key = null;
        if (null === $key) {
            $key = md5(SerializeUtil::jsonEncode(config('env')));
        }
        return $key;
    }
}
