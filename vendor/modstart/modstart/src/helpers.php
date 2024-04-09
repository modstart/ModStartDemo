<?php

use Illuminate\Support\Facades\View;
use ModStart\Admin\Config\AdminConfig;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\SerializeUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;


function modstart_version()
{
    return ModStart::$version;
}


function modstart_admin_path($path = '')
{
    return ucfirst(config('modstart.admin.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}


function modstart_admin_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.admin.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    if ('/' != $prefix) {
        $prefix .= '/';
    }
    return $prefix . $url;
}


function modstart_admin_is_tab()
{
    return boolval(View::shared('_isTab'));
}


function modstart_web_path($path = '')
{
    return ucfirst(config('modstart.web.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}


function modstart_web_full_url($url = '', $param = [])
{
    $domainUrl = Request::domainUrl();
    if ('http://localhost' == $domainUrl) {
        $domainUrl = rtrim(modstart_config('siteUrl', 'http://localhost'), '/');
    }
    return $domainUrl . modstart_web_url($url, $param);
}


function modstart_web_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.web.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}


function modstart_api_path($path = '')
{
    return ucfirst(config('modstart.api.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}



function modstart_api_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.api.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . '/' . $url;
}


function modstart_open_api_path($path = '')
{
    return ucfirst(config('modstart.openApi.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}


function modstart_open_api_url($url = '')
{
    $prefix = config('modstart.openApi.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}

function modstart_admin_config($key = null, $default = null)
{
    return AdminConfig::get($key, $default);
}

function modstart_base_path()
{
    return Request::basePath();
}

function modstart_baseurl_active($match, $output = 'active')
{
    $pass = false;
    $url = Request::basePathWithQueries();
    if (is_string($match)) {
        if (!starts_with($match, '/')) {
            $match = modstart_web_url($match);
        }
        if (\ModStart\Core\Util\ReUtil::isWildMatch($match, $url)) {
            $pass = true;
        }
    } else if (is_array($match)) {
        foreach ($match as $item) {
            if (!starts_with($item, '/')) {
                $item = modstart_web_url($item);
            }
            if (\ModStart\Core\Util\ReUtil::isWildMatch($item, $url)) {
                $pass = true;
                break;
            }
        }
    }
    if ($pass) {
        return $output;
    }
    return '';
}


function modstart_action($name, $parameters = [])
{
    try {
        return action($name, $parameters);
    } catch (Exception $e) {
        return null;
    }
}


function modstart_module_config($module, $key, $default = null)
{
    return ModuleManager::getModuleConfig($module, $key, $default);
}


function modstart_configs($keys, $default = '')
{
    if (is_string($keys)) {
        $keys = explode(',', $keys);
    }
    foreach ($keys as $key) {
        $v = modstart_config($key);
        if ($v) {
            return $v;
        }
    }
    return $default;
}


function modstart_config($key = null, $default = '', $useCache = true)
{
    static $lastKey = null;
    static $lastValue = null;
    try {
        if ($key && $key === $lastKey) {
            return $lastValue;
        }
        if (is_null($key)) {
            $lastKey = null;
            $lastValue = null;
            return app('modstartConfig');
        }
        $lastKey = $key;
        $configDefault = $default;
        if (is_array($default)) {
            $configDefault = SerializeUtil::jsonEncode($default);
        }
        $v = app('modstartConfig')->get($key, $configDefault, $useCache);
        if (true === $default || false === $default) {
            $lastValue = boolval($v);
            return $lastValue;
        }
        if (is_int($default)) {
            $lastValue = intval($v);
            return $lastValue;
        }
        if (is_array($default)) {
            $v = @json_decode($v, true);
            if (null === $v) {
                $lastValue = $default;
                return $default;
            }
            $lastValue = $v;
            return $v;
        }
        $lastValue = $v;
        return $v;
    } catch (Exception $e) {
        $lastValue = $default;
        return $default;
    }
}


function modstart_config_asset_url($key, $default = '')
{
    $value = modstart_config($key, $default);
    return \ModStart\Core\Assets\AssetsUtil::fixFull($value);
}


function modstart_module_enabled($module, $version = null)
{
    if (null === $version) {
        return ModuleManager::isModuleEnabled($module);
    } else {
        return ModuleManager::isModuleEnableMatch($module, $version);
    }
}

function L_locale_title($locale = null)
{
    if (null === $locale) {
        $locale = L_locale();
    }
    $langs = config('modstart.i18n.langs', []);
    return isset($langs[$locale]) ? $langs[$locale] : $locale;
}

function L_locale($locale = null)
{
    static $useLocale = null;
    $changingLocale = null;
    if (null !== $locale) {
        if (in_array($locale, ['en', 'zh'])) {
            $changingLocale = $locale;
        }
    }
    if (null !== $changingLocale || null === $useLocale) {
                $sessionLocaleKey = '_locale';
        if (\ModStart\App\Core\CurrentApp::is(\ModStart\App\Core\CurrentApp::ADMIN)) {
            $sessionLocaleKey = '_adminLocale';
        }
        $routeLocale = \Illuminate\Support\Facades\Request::route('locale');
        $sessionLocale = \Illuminate\Support\Facades\Session::get($sessionLocaleKey, null);
        $i18nLocale = null;
        $locale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');
        if (!\ModStart\App\Core\CurrentApp::is(\ModStart\App\Core\CurrentApp::ADMIN)
            &&
            ModuleManager::isModuleInstalled('I18n')) {
            $i18nLocale = \Module\I18n\Util\LangUtil::getDefault('shortName');
            $langTrans = \Module\I18n\Util\LangTransUtil::map();
        }
        $currentLocale = $changingLocale;
        if (empty($currentLocale)) {
            $currentLocale = $routeLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $sessionLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $i18nLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $locale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $fallbackLocale;
        }
        \Illuminate\Support\Facades\Session::put($sessionLocaleKey, $currentLocale);
        $useLocale = $currentLocale;
    }
    return $useLocale;
}


function LM($module, $name, ...$params)
{
    static $trackMissing = null;
    $useLocale = L_locale();
    if (null === $trackMissing) {
        $trackMissing = config('modstart.trackMissingLang', false);
    }
    static $langs = [];
    if (!isset($langs[$module])) {
        $langs[$module] = [];
        if ($useLocale && file_exists($file = ModuleManager::path($module, "Lang/$useLocale.php"))) {
            $langs[$module] = (require $file);
        }
    }
    if (isset($langs[$module][$name])) {
        return $langs[$module][$name];
    }
    return L($name, ...$params);
}


function L($name, ...$params)
{
    static $trackMissing = null;
    static $trackMissingData = null;
    $useLocale = L_locale();
    if (null === $trackMissing) {
        $trackMissing = config('modstart.trackMissingLang', false);
    }
    if (empty($useLocale)) {
        return $name;
    }
    if ($trackMissing && null === $trackMissingData) {
        $trackMissingData = [];
        if (file_exists($file = storage_path('cache/lang_missing.php'))) {
            $trackMissingData = (require $file);
        }
        register_shutdown_function(function () use (&$trackMissingData, $file) {
            ksort($trackMissingData);
            file_put_contents($file, '<?ph' . 'p return ' . var_export($trackMissingData, true) . ';');
        });
    }
    if ($useLocale && isset($langTrans[$useLocale][$name])) {
        if ($trackMissing && isset($trackMissingData[$name])) {
            unset($trackMissingData[$name]);
        }
        if (!empty($params)) {
            return call_user_func_array('sprintf', array_merge([$langTrans[$useLocale][$name]], $params));
        }
        return $langTrans[$useLocale][$name];
    }
    $ids = [
        'base.' . $name,
        'modstart::base.' . $name,
    ];
    $nameRaw = $name;
    if (preg_match('/^[a-z0-9]+\.(.+)$/i', $name, $mat)) {
        array_unshift($ids, $name);
        $nameRaw = $mat[1];
    }
    $env = ModStart::env();
    foreach ($ids as $id) {
        if ($env == 'laravel9') {
            $trans = trans($id, [], $useLocale);
        } else {
            $trans = trans($id, [], 'messages', $useLocale);
        }
        if ($trans !== $id) {
            if ($trackMissing && isset($trackMissingData[$nameRaw])) {
                unset($trackMissingData[$nameRaw]);
            }
            if (!empty($params)) {
                return call_user_func_array('sprintf', array_merge([$trans], $params));
            }
            return $trans;
        }
    }
    if ($trackMissing) {
        $trackMissingData[$nameRaw] = $nameRaw;
    }
    if (!empty($params)) {
        return call_user_func_array('sprintf', array_merge([$name], $params));
    }
    return $nameRaw;
}

if (!function_exists('array_build')) {
    function array_build($array, callable $callback)
    {
        $results = [];

        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }
}

if (!function_exists('starts_with')) {
    function starts_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}


if (!function_exists('array_has')) {
    function array_has($array, $key)
    {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }
}

if (!function_exists('array_except')) {
    function array_except($array, $keys)
    {
        array_forget($array, $keys);

        return $array;
    }
}

if (!function_exists('array_forget')) {
    function array_forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    $parts = [];
                }
            }

            unset($array[array_shift($parts)]);

                        $array = &$original;
        }
    }
}
if (!function_exists('ends_with')) {
    function ends_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === mb_substr($haystack, -mb_strlen($needle))) {
                return true;
            }
        }
        return false;
    }
}

if (PHP_VERSION_ID >= 80000) {
    require_once __DIR__ . '/Misc/Laravel/Input.php';
}

