<?php


namespace Module\Vendor\Provider\LBS;


use Module\Vendor\Provider\ProviderTrait;


class AddressParseProvider
{
    use ProviderTrait;

    
    public static function firstResponse($content)
    {
        $provider = self::first();
        if (!$provider) {
            return null;
        }
        return $provider->parse($content);
    }
}
