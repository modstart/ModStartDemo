<?php


namespace ModStart\Field;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;


class JsonIdItems extends AbstractField
{
    const ITEM_STYLE_TITLE = 'title';
    const ITEM_STYLE_COVER_TITLE = 'coverTitle';

    protected function setup()
    {
        $this->addVariables([
            'itemStyle' => self::ITEM_STYLE_TITLE,
            'selectUrl' => modstart_admin_url('path/to/select'),
            'previewUrl' => modstart_admin_url('path/to/preview'),
        ]);
    }

    public function itemStyle($value)
    {
        $this->addVariables(['itemStyle' => $value]);
        return $this;
    }

    public function selectUrl($value)
    {
        $this->addVariables(['selectUrl' => $value]);
        return $this;
    }

    public function previewUrl($value)
    {
        $this->addVariables(['previewUrl' => $value]);
        return $this;
    }

    public function unserializeValue($value, AbstractField $field)
    {
        if (null === $value) {
            return $value;
        }
        return @json_decode($value, true);
    }

    public function serializeValue($value, $model)
    {
        return SerializeUtil::jsonEncode($value);
    }

    public function prepareInput($value, $model)
    {
        $json = @json_decode($value, true);
        BizException::throwsIf($this->label . ' ' . L('Json Format Error'), $value && null === $json);
        return $json;
    }
}
