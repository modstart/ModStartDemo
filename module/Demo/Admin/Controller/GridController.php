<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNews;
use Module\Demo\Model\DemoNewsCategory;

class GridController extends Controller
{
    use DemoPreviewTrait;
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $this->setupDemoPreview('使用快速 CRUD 的方法，使用很少的代码创建了一个增删改查页面');
        $builder
            ->init(DemoNews::class)
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('categoryId', '分类')->optionModelTree(DemoNewsCategory::class);
                $builder->text('title', '名称')->asLink(modstart_web_url('demo/news/{id}'));
                $builder->image('cover', '封面');
                $builder->textarea('summary', '摘要')->listable(false);
                $builder->richHtml('content', '内容')->listable(false);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->title('默认数据表格');
    }
}
