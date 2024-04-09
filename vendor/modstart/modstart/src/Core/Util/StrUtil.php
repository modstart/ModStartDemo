<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;


class StrUtil
{

    public static function mask($subject, $startIndex = null, $endIndex = null, $maskChar = '*')
    {
        $strLen = mb_strlen($subject);

        if (null == $startIndex) {
            $startIndex = floor($strLen / 2);
        }
        if (null == $endIndex) {
            $endIndex = $startIndex + floor($strLen / 2);
        }

        if ($startIndex < 0) {
            $startIndex = 0;
        }
        if ($endIndex >= $strLen - 1) {
            $endIndex = $strLen - 1;
        }

        $maskedSubject = '';
        if ($startIndex > 0) {
            $maskedSubject .= mb_substr($subject, 0, $startIndex);
        }
        $maskedSubject .= str_repeat($maskChar, $endIndex - $startIndex + 1);
        if ($endIndex < $strLen - 1) {
            $maskedSubject .= mb_substr($subject, $endIndex + 1);
        }
        return $maskedSubject;

    }

    
    public static function passwordStrength($password)
    {
        $strength = 0;
        if (!empty($password)) {
            $strength++;
        }
        $password = preg_replace('/\\d+/', '', $password);
        if (!empty($password)) {
            $strength++;
        }
        $password = preg_replace('/[a-z]+/', '', $password);
        if (!empty($password)) {
            $strength++;
        }
        $password = preg_replace('/[A-Z]+/', '', $password);
        if (!empty($password)) {
            $strength++;
        }
        return $strength;
    }

    
    public static function camelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    
    public static function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }

    public static function startWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }

    
    public static function filterSpecialChars($value)
    {
        $chars = [
            "\xe2\x80\x8b",
            "\xe2\x80\x8c",
            "\xe2\x80\x8d",
        ];
        return str_replace($chars, '', $value);
    }

    public static function limit($text, $limit = 100, $end = '...')
    {
        return Str::limit($text, $limit, $end);
    }

    
    public static function mbLimit($text, $limit)
    {
        return Str::limit($text, $limit, '');
    }

    
    public static function mbLimitChars($text, $limit)
    {
        $chars = mb_str_split($text, 1, 'UTF-8');
        $count = 0;
        $str = '';
        foreach ($chars as $char) {
            $count += strlen($char);
            if ($count > $limit) {
                break;
            }
            $str .= $char;
        }
        return $str;
    }

    
    public static function mbLength($text)
    {
        if (empty($text)) {
            return 0;
        }
        return intval(mb_strlen($text, 'UTF-8'));
    }

    public static function mbLengthGt($text, $limit)
    {
        return self::mbLength($text) > $limit;
    }

    public static function mbWordwrap($string, $width = 75, $break = "\n")
    {
        if ($string === '') {
            return '';
        }

        if (strlen($string) === mb_strlen($string)) {
            return wordwrap($string, $width, $break, true);
        }

        $stringWidth = mb_strlen($string);
        $breakWidth = mb_strlen($break);

        $result = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = mb_substr($string, $current, 1);

            $possibleBreak = $char;
            if ($breakWidth !== 1) {
                $possibleBreak = mb_substr($string, $current, $breakWidth);
            }

            if ($possibleBreak === $break) {
                $result .= mb_substr($string, $lastStart, $current - $lastStart + $breakWidth);
                $current += $breakWidth - 1;
                $lastStart = $lastSpace = $current + 1;
                continue;
            }

            if ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result .= mb_substr($string, $lastStart, $current - $lastStart) . $break;
                    $lastStart = $current + 1;
                }

                $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart >= $lastSpace) {
                $result .= mb_substr($string, $lastStart, $current - $lastStart) . $break;
                $lastStart = $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result .= mb_substr($string, $lastStart, $lastSpace - $lastStart) . $break;
                $lastStart = $lastSpace = $lastSpace + 1;
                continue;
            }
        }

        if ($lastStart !== $current) {
            $result .= mb_substr($string, $lastStart, $current - $lastStart);
        }

        return $result;
    }

    public static function split($text, $spliter = ',', $spliterReplaces = ['，', ';', '；'])
    {
        $text = str_replace($spliterReplaces, $spliter, $text);
        $values = explode($spliter, $text);
        $values = array_map('trim', $values);
        $values = array_filter($values);
        return $values;
    }

    
    public static function wordSplit($content)
    {
        if (modstart_module_enabled('WordSpliter')) {
            $pcs = \Module\WordSpliter\Util\WordSpliterUtil::cut($content);
        } else {
            preg_match_all('/[\x{4e00}-\x{9fa5}]|[A-Za-z]+/u', $content, $mat);
            $pcs = array_filter($mat[0]);
        }
        return $pcs;
    }

}
