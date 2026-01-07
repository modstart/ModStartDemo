<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNewsCategory;
use Module\Demo\Util\DemoNewsCategoryUtil;
use Module\News\Util\NewsUtil;

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
                $builder->image('cover', '封面')->listable(false);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->hookChanged(function (Form $form) {
                DemoNewsCategoryUtil::clearCache();
            })
            ->title('树状数据表格')
            ->asTree()
            ->treeMaxLevel(2);
    }
}
