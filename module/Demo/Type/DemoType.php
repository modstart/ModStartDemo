<?php


namespace Module\Demo\Type;


use ModStart\Core\Type\BaseType;

class DemoType implements BaseType
{
    const VALUE_A = 'a';
    const VALUE_B = 'b';

    public static function getList()
    {
        return [
            self::VALUE_A => '值A',
            self::VALUE_B => '值B',
        ];
    }
}
