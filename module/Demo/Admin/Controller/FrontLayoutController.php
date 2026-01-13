<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Layout\Column;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FrontLayoutController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('传统栅格页面布局');

        $page->append(Box::make($this->remarkCode(), '使用说明'));

        $row = Row::make(function (Row $row) {
            $row->column(12, '<div class="ub-content-box ub-bg-a tw-text-center">col-md-12</div>');
        });
        $page->append(Box::make($row, '单行单列'));

        $row = Row::make(function (Row $row) {
            $row->column(4, '<div class="ub-content-box ub-bg-b tw-text-center">col-md-4</div>');
            $row->column(4, '<div class="ub-content-box ub-bg-b tw-text-center">col-md-4</div>');
            $row->column(4, '<div class="ub-content-box ub-bg-b tw-text-center">col-md-4</div>');
        });
        $page->append(Box::make($row, '单行三列'));

        $row = Row::make(function (Row $row) {
            $row->column(9, function (Column $column) {
                $column->row(function (Row $row) {
                    $row->column(12, '<div class="ub-content-box ub-bg-c tw-text-center margin-bottom">col-md-12</div>');
                    $row->column(4, '<div class="ub-content-box ub-bg-c tw-text-center margin-bottom">col-md-4</div>');
                    $row->column(4, '<div class="ub-content-box ub-bg-c tw-text-center margin-bottom">col-md-4</div>');
                    $row->column(4, '<div class="ub-content-box ub-bg-c tw-text-center margin-bottom">col-md-4</div>');
                });
            });
            $row->column(3, '<div class="ub-content-box ub-bg-c tw-text-center margin-bottom" style="height:4.5rem;">col-md-3</div>');
        });
        $page->append(Box::make($row, '复杂布局'));

        return $page
            ->pageTitle('页面布局');
    }

    private function remarkCode()
    {
        $code = <<<CODE
<div class="ub-html">
<p>ModStart 使用 bootstrap 的栅格系统进行布局，每行分可以为 12 个栅格(列), 每个栅格(列)也可以分为多个行</p>
<p>单行单列</p>
<pre><code> ----------------------------------
|                                  |
| col-md-12                        |
|                                  |
 ----------------------------------</code></pre>
<p>单行三列</p>
<p><pre><code> --------------------------------------
|            |            |            |
|  col-md-4  |  col-md-4  |  col-md-4  |
|            |            |            |
 --------------------------------------</code></pre></p>
<p>复杂布局</p>
<p><pre><code>{                ------> col-md-9 <------                 }{--> col-md-3 <--}
 --------------------------------------------------------------------------
|                                                         |                |
| col-md-12                                               | col-md-3       |
|                                                         |                |
|---------------------------------------------------------|                |
|                 |               |           |           |                |
| col-md-4        | col-md-4      | col-md-6  | col-md-6  |                |
|                 |               |           |           |                |
 --------------------------------------------------------------------------</code></pre></p>
</div>
CODE;
        return $code;
    }
}
