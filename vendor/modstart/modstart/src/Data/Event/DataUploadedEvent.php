<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EventUtil;


class DataUploadedEvent
{
    
    const OPT_PARAM = 'param';

    public $uploadTable;
    public $userId;
    public $category;
    public $dataId;
    public $opt;

    public static function fire($uploadTable, $userId, $category, $dataId, $opt = [])
    {
        $event = new static();
        $event->uploadTable = $uploadTable;
        $event->userId = $userId;
        $event->category = $category;
        $event->dataId = $dataId;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        return ArrayUtil::getByDotKey($this->opt, $key, $defaultValue);
    }

    public static function listen($uploadTable, $callback)
    {
        Event::listen(DataUploadedEvent::class, function (DataUploadedEvent $event) use ($uploadTable, $callback) {
            if ($event->uploadTable == $uploadTable) {
                call_user_func($callback, $event);
            }
        });
    }
}
