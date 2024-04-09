<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Session;

class SessionUtil
{
    public static function id()
    {
        return Session::getId();
    }

    private static function getDataFromSession($sessionId)
    {
        $sessionData = Session::getHandler()->read($sessionId);
        $data = @unserialize($sessionData);
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    }

    private static function saveDataToSession($sessionId, $data)
    {
        $sessionData = serialize($data);
        Session::getHandler()->write($sessionId, $sessionData);
    }

    public static function get($sessionId, $key)
    {
        $data = self::getDataFromSession($sessionId);
        return isset($data[$key]) ? $data[$key] : null;

                                                                                                    }

    public static function put($sessionId, $key, $value)
    {
        $data = self::getDataFromSession($sessionId);
        $data[$key] = $value;
        self::saveDataToSession($sessionId, $data);

                                                                                            }

    public static function forget($sessionId, $key)
    {
        $data = self::getDataFromSession($sessionId);
        unset($data[$key]);
        self::saveDataToSession($sessionId, $data);

                                                                                            }

    public static function clear($sessionId)
    {
        self::saveDataToSession($sessionId, []);

                                                                                            }
}
