<?php

namespace ModStart\Core\Util;

use Intervention\Image\ImageManagerStatic as Image;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Provider\FontProvider;


class ImageUtil
{
    public static function base64Src($imageContent, $type = 'png')
    {
        if (!in_array($type, ['png', 'gif', 'jpg', 'jpeg'])) {
            return null;
        }
        return 'data:' . FileUtil::mime($type) . ';base64,' . base64_encode($imageContent);
    }

    public static function limitSizeAndDetectOrientation($path, $maxWidth = 1000, $maxHeight = 1000)
    {
        $extensionPermit = [
            'jpg', 'jpeg', 'png', 'gif',
        ];
        $ext = FileUtil::extension($path);
        if (!in_array($ext, $extensionPermit)) {
            return;
        }
        try {
            $changed = false;
            $exif = @exif_read_data($path);
            $image = Image::make($path);
            if (!empty($exif['Orientation'])) {
                switch (intval($exif['Orientation'])) {
                    case 2:
                        $image->flip();
                        $changed = true;
                        break;
                    case 3:
                        $image->rotate(180);
                        $changed = true;
                        break;
                    case 4:
                        $image->rotate(180);
                        $image->flip();
                        $changed = true;
                        break;
                    case 5:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 6:
                        $image->rotate(-90);
                        $changed = true;
                        break;
                    case 7:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 8:
                        $image->rotate(90);
                        $changed = true;
                        break;
                }
            }

            $width = $image->width();
            $height = $image->height();
            if ($width > $maxWidth || $height > $maxHeight) {
                $changed = true;
                if ($width > $maxWidth) {
                    $image->resize($maxWidth, intval($maxWidth * $height / $width));
                }
                if ($height > $maxHeight) {
                    $image->resize(intval($maxHeight * $width / $height), $maxHeight);
                }
            }

            if ($changed) {
                $image->save($path);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    
    public static function watermark($image, $type, $content, $option = [])
    {
        $option = array_merge([
            'return' => false,                                              'mode' => 'single',                                             'rotate' => 'horizontal',                                       'gapX' => 50,                                                   'gapY' => 30,                                                   'minSizePx' => 100,                                 
            'textColor' => '#FFFFFF',                                       'textOpacity' => 40,                                            'textSize' => 5,                                                'textFont' => FontProvider::firstLocalPathOrFail(), 
            'imageSize' => 20,                                              'imageOpacity' => 40,                                       ], $option);
        try {
            BizException::throwsIf('Image not exists', !file_exists($image));
            BizException::throwsIf('watermark type error', !in_array($type, ['image', 'text']));
            BizException::throwsIf('watermark content empty', empty($content));
            BizException::throwsIf('watermark text color error', !preg_match('/^#[0-9a-fA-F]{6}$/', $option['textColor']));
        } catch (BizException $e) {
            return Response::generateError($e->getMessage());
        }

        $changed = false;

        $data = [
            'processed' => false,
            'success' => false,
            'message' => '',
        ];

        $img = Image::make($image);
        $width = $img->width();
        $height = $img->height();
        if ($width < $option['minSizePx'] || $height < $option['minSizePx']) {
            $img->destroy();
            $data['message'] = '图片尺寸过小，不加水印';
            return Response::generateSuccessData($data);
        }

        $info = self::calcWatermarkPositionInfo($width, $height, $option);
        $normalPx = $info['normalPx'];
        $points = $info['points'];
        $option = $info['option'];

        switch ($type) {
            case 'text':
                $option['_textFont'] = FileUtil::savePathToLocalTemp($option['textFont']);
                $textColor = $option['textColor'] . sprintf('%02x', intval($option['textOpacity'] * 255 / 100));
                $option['_textColor'] = ColorUtil::hexToRgba($textColor);
                $option['_textSize'] = intval($option['textSize'] * $normalPx);
                foreach ($points as $point) {
                    $img->text($content, $point['x'], $point['y'],
                        function ($font) use ($option) {
                            $font->file($option['_textFont']);
                            $font->size($option['_textSize']);
                            $font->color($option['_textColor']);
                            $font->align('center');
                            $font->valign('center');
                            if ('oblique' == $option['rotate']) {
                                $font->angle(45);
                            }
                        });
                }
                $changed = true;
                break;
            case 'image':
                $localWater = FileUtil::savePathToLocalTemp($content);
                BizException::throwsIf('watermark image not exists', !file_exists($localWater));
                $watermark = Image::make($localWater);
                $limit = intval($option['imageSize'] * $normalPx);
                $waterWidth = $watermark->width();
                $waterHeight = $watermark->height();
                if ($waterWidth > $waterHeight) {
                    $waterHeight = intval($limit * $waterHeight / $waterWidth);
                    $waterWidth = $limit;
                } else {
                    $waterWidth = intval($limit * $waterWidth / $waterHeight);
                    $waterHeight = $limit;
                }
                $watermark->resize($waterWidth, $waterHeight);
                $watermark->opacity($option['imageOpacity']);
                if ('oblique' == $option['rotate']) {
                    $watermark->rotate(45);
                }
                foreach ($points as $point) {
                    $img->insert($watermark, 'top-left',
                        intval($point['x'] - $waterWidth / 2),
                        intval($point['y'] - $waterHeight / 2)
                    );
                }
                $changed = true;
                break;
        }
        if ($option['return']) {
            $result = $img->response('png');
            $img->destroy();
            return $result;
        }
        $data['processed'] = true;
        if ($changed) {
            $data['success'] = true;
            $img->save($image);
        }
        $img->destroy();
        return Response::generateSuccessData($data);
    }

    public static function info($file)
    {
        $img = Image::make($file);
        return [
            'width' => $img->width(),
            'height' => $img->height(),
            'size' => $img->filesize(),
        ];
    }

    
    public static function calcWatermarkPositionInfo($width, $height, array $option)
    {
        $normalPx = min($width, $height) / 100;

        $points = [];
        switch ($option['mode']) {
            case 'single':
                $points[] = [
                    'x' => intval($width / 2),
                    'y' => intval($height / 2)
                ];
                break;
            case 'repeat':
                $option['_gapX'] = intval($option['gapX'] * $normalPx);
                $option['_gapY'] = intval($option['gapY'] * $normalPx);
                $xs = [];
                for ($d = 0, $start = intval($width / 2); $start - $d > 0 && $start + $d < $width; $d += $option['_gapX']) {
                    $xs[] = $start + $d;
                    if ($d > 0) {
                        $xs[] = $start - $d;
                    }
                }
                for ($d = 0, $start = intval($height / 2); $start - $d > 0 && $start + $d < $height; $d += $option['_gapY']) {
                    foreach ($xs as $x) {
                        $points[] = ['x' => $x, 'y' => $start + $d];
                        if ($d > 0) {
                            $points[] = ['x' => $x, 'y' => $start - $d];
                        }
                    }
                }
                break;
        }
        return [
            'normalPx' => $normalPx,
            'points' => $points,
            'option' => $option,
        ];
    }

}
