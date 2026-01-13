<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Layout\Row;
use ModStart\Module\ModuleManager;
use ModStart\Widget\Box;
use ModStart\Widget\Chart\Bar;
use ModStart\Widget\Chart\Chart;
use ModStart\Widget\Chart\Line;
use ModStart\Widget\Chart\Pie;
use ModStart\Widget\Chart\Scatter;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FrontEchartController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('支持 ECharts 组件化使用');

        $page->row(function (Row $row) {
            $row->column(6, Box::make(
                Line::make()->random()->ySeries(1, RandomUtil::numberCollection()),
                '<i class="iconfont icon-chart"></i> 折线图'
            ));
            $row->column(6, Box::make(
                Bar::make()->random()->ySeries(1, RandomUtil::numberCollection()),
                '<i class="iconfont icon-chart"></i> 柱状图'
            ));
            $row->column(6, Box::make(
                Pie::make()->random(),
                '<i class="iconfont icon-chart"></i> 饼图'
            ));
            $row->column(6, Box::make(
                Scatter::make()->random()->ySeries(1, RandomUtil::numberCollection()),
                '<i class="iconfont icon-chart"></i> 散点图'
            ));
                        $demoFiles = FileUtil::listFiles(ModuleManager::path('Demo', 'Res/EchartDemo'), '*.js');
            foreach ($demoFiles as $f) {
                $row->column(4, Box::make(
                    Chart::make()->option(file_get_contents($f['pathname'])),
                    '<i class="iconfont icon-chart"></i> 自定义-' . $f['filename']
                ));
            }
        });

        return $page
            ->pageTitle('ECharts 集成');
    }
}
