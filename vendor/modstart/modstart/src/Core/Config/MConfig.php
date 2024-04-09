<?php

namespace ModStart\Core\Config;


use ModStart\Core\Util\SerializeUtil;


abstract class MConfig
{
    
    public abstract function get($key, $defaultValue = '', $useCache = true);

    
    public abstract function set($key, $value);

    public abstract function remove($key);

    public abstract function has($key);

    public abstract function all($prefix = null);

    public function getWithEnv($key, $defaultValue = null)
    {
        $value = config('env.CONFIG_' . $key);
        if (null === $value) {
            $value = $this->get($key);
        }
        if (empty($value)) {
            return $defaultValue;
        }
        return $value;
    }

    public function setArray($key, $value)
    {
        $this->set($key, SerializeUtil::jsonEncode($value));
    }

    public function getArray($key, $defaultValue = [], $useCache = true)
    {
        $value = $this->get($key, SerializeUtil::jsonEncode($defaultValue), $useCache);
        $value = @json_decode($value, true);
        if (!is_array($value) || empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function getBoolean($key, $defaultValue = false)
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return $value ? true : false;
    }

    public function getInteger($key, $defaultValue = 0)
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return intval($value);
    }

    public function getString($key, $defaultValue = '')
    {
        $value = $this->get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return '' . $value;
    }
}

