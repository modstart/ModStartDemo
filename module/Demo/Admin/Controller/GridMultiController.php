<?php


namespace Module\Demo\Admin\Controller;


use Module\Demo\Model\DemoTest;
use Module\Demo\Model\DemoTestCategory;
use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminDetail;
use ModStart\Admin\Concern\HasAdminForm;
use ModStart\Admin\Concern\HasAdminGrid;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasPageTitleInfo;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class GridMultiController extends Controller
{
    use HasPageTitleInfo;
    use HasAdminGrid;
    use HasAdminForm;
    use HasAdminDetail;
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('包含了多个Grid，每个Grid可独立处理');
        $grid1 = $this->grid1();
        $grid2 = $this->grid2();
        if (Request::isPost()) {
            switch (InputPackage::buildFromInput()->getTrimString('_grid')) {
                case 'grid1':
                    return $grid1->request();
                case 'grid2':
                    return $grid2->request();
            }
        }
        return $page
            ->pageTitle('多个数据表格')
            ->append(Box::make($grid1, '测试分类'))
            ->append(Box::make($grid2, '测试管理'));
    }

    public function grid1()
    {
        $grid = Grid::make(DemoTestCategory::class);
        $grid->id('id', 'ID');
        $grid->text('title', '标题');
        $grid->title('测试分类');
        $grid->urlGrid(modstart_admin_url('demo/grid_multi', ['_grid' => 'grid1']));
        $grid->canAdd(false)->canEdit(false)->canDelete(false);
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
        });
        return $grid;
    }

    public function grid2()
    {
        $grid = Grid::make(DemoTest::class);
        $grid->id('id', 'ID');
        $grid->select('categoryId', '分类')->optionModelTree(DemoTestCategory::class);
        $grid->text('title', '标题');
        $grid->richHtml('content', '内容');
        $grid->title('测试管理');
        $grid->urlGrid(modstart_admin_url('demo/grid_multi', ['_grid' => 'grid2']));
        $grid->canAdd(false)->canEdit(false)->canDelete(false);
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('id', 'ID');
            $filter->like('title', '标题');
        });
        return $grid;
    }
}
