<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\DashboardItem;
use ModStart\Layout\Column;
use ModStart\Layout\Row;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Admin\Widget\DemoCopyrightWidget;
use Module\Demo\Admin\Widget\DevicesWidget;
use Module\Demo\Admin\Widget\TasksWidget;
use Module\Demo\Admin\Widget\UsersWidget;

class WidgetStaticController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('静态 Widget，在页面渲染时候计算完成', [
            'codes' => [
                [
                    'name' => 'Widget',
                    'type' => 'php',
                    'path' => 'module/Demo/Admin/Widget/DemoCopyrightWidget.php',
                ],
                [
                    'name' => 'Blade',
                    'type' => 'html',
                    'path' => 'module/Demo/View/admin/widget/copyright.blade.php',
                ],
            ]
        ]);

        $page->append(new DemoCopyrightWidget());

        return $page
            ->pageTitle('静态 Widget');
    }
}
