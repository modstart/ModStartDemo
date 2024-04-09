<?php

namespace Module\Demo\Admin\Widget;

use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\ModStart;
use ModStart\Widget\AbstractRawWidget;
use ModStart\Widget\Traits\HasRequestTrait;
use ModStart\Widget\Traits\HasVueFileTrait;

class TasksWidget extends AbstractRawWidget
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
        switch ($time) {
            case '30':
            case '7':
            default:
                foreach ([
                             '进行中', '已完成', '已延期',
                         ] as $os) {
                    $records[] = [
                        'value' => rand(10, 1000),
                        'name' => $os
                    ];
                }
                break;
        }
        return Response::generateSuccessData([
            'records' => $records,
            'time' => $time,
        ]);
    }

    public function permit()
    {
        return AdminPermission::permit('\Module\Demo\Admin\Controller\WidgetController@index');
    }


}
