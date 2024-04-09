<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNewsCategory;

class GridTreeController extends Controller
{
    use DemoPreviewTrait;
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $this->setupDemoPreview('树状分类，理论支持无限级分类');
        $builder
            ->init(DemoNewsCategory::class)
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->text('title', '名称');
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->title('树状数据表格')
            ->asTree()
            ->treeMaxLevel(2);
    }

}
