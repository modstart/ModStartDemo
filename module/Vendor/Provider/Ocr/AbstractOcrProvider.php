<?php


namespace Module\Vendor\Provider\Ocr;


abstract class AbstractOcrProvider
{
    abstract public function name();

    abstract public function title();

    
    abstract public function getText($imageUrl, $format = null, $param = []);

}
