<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Admin\Widget\DevicesWidget;
use Module\Demo\Admin\Widget\TasksWidget;

class WidgetVueController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('可以动态与后台交互数据，像写 Vue 组件一样写 Widget', [
            'codes' => [
                [
                    'name' => 'DevicesWidget',
                    'type' => 'php',
                    'path' => 'module/Demo/Admin/Widget/DevicesWidget.php',
                ],
                [
                    'name' => 'DevicesWidget-Vue',
                    'type' => 'html',
                    'path' => 'module/Demo/Admin/Widget/DevicesWidget.vue',
                ],
                [
                    'name' => 'TasksWidget',
                    'type' => 'php',
                    'path' => 'module/Demo/Admin/Widget/TasksWidget.php',
                ],
                [
                    'name' => 'TasksWidget-Vue',
                    'type' => 'html',
                    'path' => 'module/Demo/Admin/Widget/TasksWidget.vue',
                ],
            ]
        ]);

        $page->append(new DevicesWidget());
        $page->append(new TasksWidget());

        return $page
            ->pageTitle('Vue 单文件 Widget');
    }
}
