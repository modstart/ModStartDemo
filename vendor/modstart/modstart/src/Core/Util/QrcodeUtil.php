<?php

namespace ModStart\Core\Util;

use BaconQrCode\Encoder\Encoder;
use ModStart\Core\Exception\BizException;
use ModStart\ModStart;


class QrcodeUtil
{
    
    public static function png($content, $size = 200)
    {
        if (class_exists(\BaconQrCode\Renderer\Image\Png::class)) {
            $renderer = new \BaconQrCode\Renderer\Image\Png();
            $renderer->setMargin(0);
            $renderer->setHeight($size);
            $renderer->setWidth($size);
        } else {
            BizException::throwsIf('Please Install imagick extension', !extension_loaded('imagick'));
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
                new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
            );
        }
        $writer = new \BaconQrCode\Writer($renderer);
        return $writer->writeString($content,'UTF-8');
    }

    
    public static function pngBase64String($content, $size = 200)
    {
        return 'data:image/png;base64,' . base64_encode(self::png($content, $size));
    }
}
