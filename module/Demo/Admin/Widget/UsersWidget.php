<?php

namespace Module\Demo\Admin\Widget;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\TimeUtil;
use ModStart\ModStart;
use ModStart\Widget\AbstractRawWidget;
use ModStart\Widget\Traits\HasRequestTrait;
use ModStart\Widget\Traits\HasVueFileTrait;

class UsersWidget extends AbstractRawWidget
{
    use HasVueFileTrait;
    use HasRequestTrait;

    public function contentRenderBefore()
    {
        ModStart::js('asset/vendor/echarts/echarts.all.js');
    }

    public function initParam()
    {
        return Response::tryGetData($this->request());
    }

    public function request()
    {
        $input = InputPackage::buildFromInput();
        $time = $input->getTrimString('time');
        $records = [];
        $endTs = strtotime(TimeUtil::todayStart());
        switch ($time) {
            case '365':
                $startTs = $endTs - 365 * 24 * 3600;
                break;
            case '30':
                $startTs = $endTs - 30 * 24 * 3600;
                break;
            case '7':
            default:
                $startTs = $endTs - 7 * 24 * 3600;
                break;
        }
        $total = 0;
        for ($ts = $startTs; $ts <= $endTs; $ts += 24 * 3600) {
            $count = mt_rand(10, 300);
            $total += $count;
            $records[] = [
                'time' => date('Y-m-d', $ts),
                'value' => $count,
            ];
        }
        return Response::generateSuccessData([
            'records' => $records,
            'time' => $time,
            'total' => $total,
        ]);
    }

    public function permit()
    {
        return AdminPermission::permit('\Module\Demo\Admin\Controller\WidgetController@index');
    }


}
