<?php

namespace Module\Vendor\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\RedisUtil;
use Module\Vendor\Model\Atomic;


class AtomicUtil
{
    private static function autoCleanDB()
    {
        if (RandomUtil::percent(20)) {
            ModelUtil::model(Atomic::class)->where('expire', '<', time())->delete();
        }
    }


    
    public static function produce($name, $value, $expire = 3600)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            RedisUtil::set($hash, $value);
            RedisUtil::expire($hash, $expire);
        } else {
            if (ModelUtil::exists(Atomic::class, ['name' => $name])) {
                ModelUtil::update(Atomic::class, ['name' => $name], ['value' => $value, 'expire' => time() + $expire]);
            } else {
                ModelUtil::insertIgnoreUnique(Atomic::class, ['name' => $name, 'value' => $value, 'expire' => time() + $expire]);
            }
            self::autoCleanDB();
        }
    }

    
    public static function consume($name)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            if (RedisUtil::decr($hash) >= 0) {
                return true;
            }
            return false;
        } else {
            self::autoCleanDB();
            ModelUtil::transactionBegin();
            $atomic = ModelUtil::getWithLock(Atomic::class, ['name' => $name]);
            if (empty($atomic)) {
                ModelUtil::transactionCommit();
                return false;
            }
            if ($atomic['expire'] < time() || $atomic['value'] < 0) {
                ModelUtil::delete(Atomic::class, ['name' => $name]);
                ModelUtil::transactionCommit();
                return false;
            }
            ModelUtil::update(Atomic::class, ['name' => $name], ['value' => $atomic['value'] - 1]);
            ModelUtil::transactionCommit();
            return true;
        }
    }

    
    public static function remove($name)
    {
        if (RedisUtil::isEnable()) {
            $hash = "Atomic:$name";
            RedisUtil::delete($hash);
        } else {
            ModelUtil::delete(Atomic::class, ['name' => $name]);
        }
    }

    
    public static function acquire($name, $expire = 30)
    {
        if (RedisUtil::isEnable()) {
            $key = "Atomic:$name";
            if (RedisUtil::setnx($key, time() + $expire)) {
                RedisUtil::expire($key, $expire);
                return true;
            }
            $ts = RedisUtil::get($key);
            if ($ts < time()) {
                RedisUtil::delete($key);
                return self::acquire($name, $expire);
            }
            return false;
        } else {
            self::autoCleanDB();
            ModelUtil::transactionBegin();
            $atomic = ModelUtil::getWithLock(Atomic::class, ['name' => $name]);
            $ts = time() + $expire;
            if (empty($atomic)) {
                ModelUtil::insert(Atomic::class, [
                    'name' => $name,
                    'value' => 1,
                    'expire' => $ts
                ]);
                ModelUtil::transactionCommit();
                return true;
            }
            if ($atomic['expire'] < time()) {
                ModelUtil::update(Atomic::class, ['name' => $name], [
                    'value' => 1,
                    'expire' => $ts
                ]);
                ModelUtil::transactionCommit();
                return true;
            }
            ModelUtil::transactionCommit();
            return false;
        }
    }

    
    public static function release($name)
    {
        if (RedisUtil::isEnable()) {
            $key = "Atomic:$name";
            RedisUtil::delete($key);
        } else {
            ModelUtil::delete(Atomic::class, ['name' => $name]);
        }
    }
}
