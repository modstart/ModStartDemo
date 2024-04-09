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

class WidgetController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('提供一些基础的数据统计卡片');

        $page->row(function (Row $row) {
            $row->column(8, function (Column $column) {
                $column->append(new DemoCopyrightWidget());
            });
            $row->column(4, function (Column $column) {
                $column->append(new DevicesWidget());
            });
            $row->column(3, function (Column $column) {
                $column->append(DashboardItem::makeTitleDataList(
                    'iconfont icon-users',
                    '用户',
                    [
                        [
                            'title' => '总数',
                            'value' => rand(10000, 100000),
                        ],
                        [
                            'title' => '昨日新增',
                            'value' => rand(100, 1000),
                        ],
                        [
                            'title' => '本月新增',
                            'value' => rand(1000, 5000),
                        ],
                    ]
                ));
            });
            $row->column(3, function (Column $column) {
                $column->append(DashboardItem::makeTitleDataList(
                    'iconfont icon-users',
                    '文章',
                    [
                        [
                            'title' => '总数',
                            'value' => rand(10000, 100000),
                        ],
                        [
                            'title' => '昨日新增',
                            'value' => rand(100, 1000),
                        ],
                        [
                            'title' => '本月新增',
                            'value' => rand(1000, 5000),
                        ],
                    ]
                ));
            });
            $row->column(3, function (Column $column) {
                $column->append(DashboardItem::makeTitleDataList(
                    'iconfont icon-users',
                    '评论',
                    [
                        [
                            'title' => '总数',
                            'value' => rand(10000, 100000),
                        ],
                        [
                            'title' => '昨日新增',
                            'value' => rand(100, 1000),
                        ],
                        [
                            'title' => '本月新增',
                            'value' => rand(1000, 5000),
                        ],
                    ]
                ));
            });
            $row->column(3, function (Column $column) {
                $column->append(DashboardItem::makeTitleDataList(
                    'iconfont icon-users',
                    '访问',
                    [
                        [
                            'title' => '总数',
                            'value' => rand(10000, 100000),
                        ],
                        [
                            'title' => '昨日新增',
                            'value' => rand(100, 1000),
                        ],
                        [
                            'title' => '本月新增',
                            'value' => rand(1000, 5000),
                        ],
                    ]
                ));
            });
            $row->column(6, function (Column $column) {
                $column->append(new UsersWidget());
            });
            $row->column(6, function (Column $column) {
                $column->append(new TasksWidget());
            });
        });
        return $page
            ->pageTitle('数据统计卡片');
    }
}
