<?php


namespace ModStart\Field;


class Audio extends AbstractField
{
    const MODE_DEFAULT = 'default';
    const MODE_RAW = 'raw';

    protected $width = 80;

    protected function setup()
    {
        $this->addVariables([
            'previewPlay' => false,
            'category' => 'audio',
            'mode' => self::MODE_DEFAULT,
            'server' => modstart_admin_url('data/file_manager/audio'),
        ]);
    }

    public function previewPlay($enable)
    {
        $this->addVariables(['previewPlay' => $enable]);
        if ($enable) {
            $this->width = 400;
        }
        return $this;
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    
    public function mode($mode)
    {
        $this->addVariables(['mode' => $mode]);
        return $this;
    }
}
