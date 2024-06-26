<?php

namespace Module\Vendor\Log;


use Illuminate\Support\Facades\Log;
use ModStart\Core\Util\SerializeUtil;

class Logger
{
    private static function rotateLogPath($filename, $logType, $base)
    {
        $date = date('Ymd');
        return rtrim($base, '/') . '/' . $filename . ($logType ? '_' . $logType : '') . '_' . $date . '.log';
    }

    private static function rotateLogPattern($filename, $logType, $base)
    {
        return rtrim($base, '/') . '/' . $filename . ($logType ? '_' . $logType : '') . '_[0-9][0-9][0-9][0-9]*.log';
    }

    private static function rotateLogClean($filename, $logType, $maxKeeps, $base)
    {
        $pattern = self::rotateLogPattern($filename, $logType, $base);
        $logFiles = glob($pattern);
        if (empty($logFiles)) {
            return;
        }
        usort($logFiles, function ($a, $b) {
            return strcmp($b, $a);
        });
        foreach (array_slice($logFiles, $maxKeeps) as $file) {
            Log::info("Vendor.LoggerClean - " . $file);
            @unlink($file);
        }
    }

    public static function rotateLog($filename, $logType = null, $maxKeeps = 7, $base = null)
    {
        if (null === $base) {
            $base = storage_path('logs');
        }
        $path = self::rotateLogPath($filename, $logType, $base);
        if (!file_exists($path)) {
            self::rotateLogClean($filename, $logType, $maxKeeps, $base);
        }
        return $path;
    }

    public static function write($file, $type, $label, $msg)
    {
        if (!is_string($msg)) {
            $msg = SerializeUtil::jsonEncode($msg);
        }
        $string = "[" . sprintf('%05d', getmypid()) . "] " . date('Y-m-d H:i:s') . " - $label" . ($msg ? " - $msg" : '');
        $logPath = self::rotateLog($file, $type);
        @file_put_contents($logPath, $string . "\n", FILE_APPEND);
        return $string;
    }

    
    public static function info($file, $label, $msg = null)
    {
        return self::write($file, 'info', $label, $msg);
    }

    
    public static function error($file, $label, $msg = null)
    {
        return self::write($file, 'error', $label, $msg);
    }
}
