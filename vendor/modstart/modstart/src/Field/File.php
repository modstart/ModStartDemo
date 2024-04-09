<?php


namespace ModStart\Field;


class File extends AbstractField
{
    const MODE_DEFAULT = 'default';
    const MODE_RAW = 'raw';

    protected function setup()
    {
        $this->addVariables([
            'category' => 'file',
            'mode' => self::MODE_DEFAULT,
            'server' => modstart_admin_url('data/file_manager/file'),
        ]);
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
