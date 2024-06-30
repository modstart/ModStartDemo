<?php


namespace Module\Vendor\Schedule;


use ModStart\Core\Util\PlatformUtil;
use ModStart\Core\Util\ShellUtil;
use Module\Vendor\Provider\Schedule\AbstractScheduleBiz;

class TempFileCleanScheduleBiz extends AbstractScheduleBiz
{
    public function cron()
    {
        return $this->cronEveryHour();
    }

    public function title()
    {
        return 'temp文件自动清理';
    }

    public function run()
    {
        $tempPath = public_path('temp');
        if (PlatformUtil::isWindows()) {
                                } else {
            $command = 'find "' . $tempPath . '" -maxdepth 1 -mtime +1 -exec rm -rfv {} \\; > /dev/null 2>&1 &';
        }
        if (!empty($command)) {
            ShellUtil::run($command);
        }
    }

}
