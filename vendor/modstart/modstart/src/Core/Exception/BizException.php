<?php

namespace ModStart\Core\Exception;


class BizException extends \Exception
{
    public $param = [];

    
    public static function throws($msg, $param = [])
    {
        $e = new BizException($msg);
        $e->param = $param;
        throw $e;
    }

    
    public static function throwsIf($msg, $condition, $param = [])
    {
        if ($condition) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    
    public static function throwsIfEmpty($msg, $object, $param = [])
    {
        if (empty($object)) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    
    public static function throwsIfNotEmpty($msg, $object, $param = [])
    {
        if (!empty($object)) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    
    public static function throwsIfResponseError($response, $prefix = '', $param = [])
    {
        if ($prefix) {
            $prefix = $prefix . ':';
        }
        if (empty($response)) {
            $e = new BizException($prefix . 'Response Empty');
            $e->param = $param;
            throw $e;
        }
        if ($response['code']) {
            $e = new BizException($prefix . $response['msg']);
            $e->param = $param;
            throw $e;
        }
    }

    
    public static function throwsIfMessageMatch($error, $messagePatterns, $messagePrefix = '', $isRegex = false)
    {
        if ($error instanceof \Exception) {
            $error = $error->getMessage();
        }
        foreach ($messagePatterns as $pattern => $message) {
            if ($isRegex) {
                if (preg_match($pattern, $error)) {
                    BizException::throws($messagePrefix . $message);
                }
            } else {
                if (strpos($error, $pattern) !== false) {
                    BizException::throws($messagePrefix . $message);
                }
            }
        }
    }

}
