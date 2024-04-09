<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminDetail;
use ModStart\Admin\Concern\HasAdminForm;
use ModStart\Admin\Concern\HasAdminGrid;
use ModStart\Detail\Detail;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasPageTitleInfo;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNews;
use Module\Demo\Model\DemoNewsCategory;

class GridRawController extends Controller
{
    use DemoPreviewTrait;

    use HasPageTitleInfo;
    use HasAdminGrid;
    use HasAdminForm;
    use HasAdminDetail;

    public function grid()
    {
        $this->setupDemoPreview('使用 Grid、Form、Detail 独立控制表格、表单、详情页面的显示，每个页面可高度定制');
        $grid = Grid::make(DemoNews::class);
        $grid->id('id', 'ID');
        $grid->select('categoryId', '分类')->optionModelTree(DemoNewsCategory::class);
        $grid->text('title', '标题');
        $grid->richHtml('content', '内容');
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
        });
        $grid->title('独立控制表格');
        return $grid;
    }

    public function form()
    {
        $form = Form::make(DemoNews::class);
        $form->select('categoryId', '分类')->optionModelTree(DemoNewsCategory::class);
        $form->text('title', '标题');
        $form->richHtml('content', '内容');
        return $form;
    }

    public function detail()
    {
        $detail = Detail::make(DemoNews::class);
        $detail->select('categoryId', '分类')->optionModelTree(DemoNewsCategory::class);
        $detail->text('title', '标题');
        $detail->richHtml('content', '内容');
        return $detail;
    }
}
