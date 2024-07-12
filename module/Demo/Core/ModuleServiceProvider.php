<?php

namespace Module\Demo\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use Module\Vendor\Admin\Widget\AdminWidgetLink;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        AdminMenu::register(function () {
            return [
                [
                    'title' => '数据表格 Grid',
                    'icon' => 'table',
                    'sort' => 150,
                    'children' => [
                        [
                            'title' => '默认数据表格',
                            'url' => '\Module\Demo\Admin\Controller\GridController@index',
                        ],
                        [
                            'title' => '独立控制表格',
                            'url' => '\Module\Demo\Admin\Controller\GridRawController@index',
                        ],
                        [
                            'title' => '多个数据表格',
                            'url' => '\Module\Demo\Admin\Controller\GridMultiController@index',
                        ],
                        [
                            'title' => '树状数据表格',
                            'url' => '\Module\Demo\Admin\Controller\GridTreeController@index',
                        ],
                        [
                            'title' => '自定义视图',
                            'url' => '\Module\Demo\Admin\Controller\GridCustomItemController@index',
                        ],
                        [
                            'title' => '表格自定义',
                            'url' => '\Module\Demo\Admin\Controller\GridOperateController@index',
                        ],
                    ]
                ],
                [
                    'title' => '数据表单 Form',
                    'icon' => 'description',
                    'sort' => 151,
                    'children' => [
                        [
                            'title' => '默认数据表单',
                            'url' => '\Module\Demo\Admin\Controller\FormController@index',
                        ],
                        [
                            'title' => '系统配置表单',
                            'url' => '\Module\Demo\Admin\Controller\FormConfigController@index',
                        ],
                        [
                            'title' => '复杂布局表单',
                            'url' => '\Module\Demo\Admin\Controller\FormLayoutController@index',
                        ],
                        [
                            'title' => '表单动态显示',
                            'url' => '\Module\Demo\Admin\Controller\FormDynamicController@index',
                        ],
                        [
                            'title' => '弹窗表单',
                            'url' => '\Module\Demo\Admin\Controller\FormDialogController@index',
                        ],
                        [
                            'title' => '表单所有组件',
                            'url' => '\Module\Demo\Admin\Controller\FormFieldController@index',
                        ],
                    ]
                ],
                [
                    'title' => '组件支持 Widget',
                    'icon' => 'chart',
                    'sort' => 152,
                    'children' => [
                        [
                            'title' => '数据统计卡片',
                            'url' => '\Module\Demo\Admin\Controller\WidgetController@index',
                        ],
                        [
                            'title' => '静态 Widget',
                            'url' => '\Module\Demo\Admin\Controller\WidgetStaticController@index',
                        ],
                        [
                            'title' => 'Vue 单文件 Widget',
                            'url' => '\Module\Demo\Admin\Controller\WidgetVueController@index',
                        ],
                    ]
                ],
                [
                    'title' => '开发示例页面',
                    'icon' => 'cube',
                    'sort' => 153,
                    'children' => [
                        [
                            'title' => '页面布局',
                            'url' => '\Module\Demo\Admin\Controller\FrontLayoutController@index',
                        ],
                        [
                            'title' => 'Icon 图标',
                            'url' => '\Module\Demo\Admin\Controller\FrontIconController@index',
                        ],
                        [
                            'title' => 'Tailwind CSS',
                            'url' => '\Module\Demo\Admin\Controller\FrontTwController@index',
                        ],
                        [
                            'title' => 'Vue+ElementUI 集成',
                            'url' => '\Module\Demo\Admin\Controller\FrontVueController@index',
                        ],
                        [
                            'title' => 'ECharts 集成',
                            'url' => '\Module\Demo\Admin\Controller\FrontEchartController@index',
                        ],
                        [
                            'title' => '404页面',
                            'url' => '\Module\Demo\Admin\Controller\FrontPageController@page404',
                        ],
                        [
                            'title' => '500页面',
                            'url' => '\Module\Demo\Admin\Controller\FrontPageController@page500',
                        ],
                    ]
                ],
                [
                    'title' => '开发者文档',
                    'icon' => 'code',
                    'sort' => 154,
                    'url' => '\Module\Demo\Admin\Controller\DocController@index',
                ],
            ];
        });
        AdminWidgetLink::register(function () {
            return AdminWidgetLink::build('Demo模块', [
                ['链接', modstart_web_url('demo')],
            ]);
        });
    }

    
    public function register()
    {

    }
}
