<?php


namespace Module\Vendor\Provider\LBS;


abstract class AbstractAddressParseProvider
{
    abstract public function name();

    abstract public function title();

    
    abstract public function parse($content, $param = []);

}
