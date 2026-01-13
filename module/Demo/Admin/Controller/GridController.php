<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\ArrayPackage;
use ModStart\Core\Input\Response;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoTest;
use Module\Demo\Model\DemoTestCategory;
use Module\Vendor\QuickRun\Export\ImportHandle;

class GridController extends Controller
{
    use DemoPreviewTrait;
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $this->setupDemoPreview('使用快速 CRUD 的方法，使用很少的代码创建了一个 新增、查看、更新、删除、导入 页面');
        $builder
            ->init(DemoTest::class)
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('categoryId', '分类')->optionModelTree(DemoTestCategory::class);
                $builder->text('title', '标题')->asLink(modstart_web_url('demo/test/{id}'));
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
            ->canImport(true)
            ->title('默认数据表格');
    }

    public function import(ImportHandle $handle)
    {
        $heads = [
            '分类ID',
            '标题',
            '内容',
        ];
        $templateData = [];
        $templateData[] = [
            '1',
            '示例标题1',
            '示例内容1',
        ];

        return $handle
            ->withPageTitle('内容导入')
            ->withTemplateName('内容导入模板')
            ->withTemplateData($templateData)
            ->withHeadTitles($heads)
            ->handleImport(function ($data, $param) {
                $package = ArrayPackage::build($data);
                $record = [];
                $record['categoryId'] = $package->nextInteger();
                $record['title'] = $package->nextTrimString();
                $record['content'] = $package->nextTrimString();
                ModelUtil::insert(DemoTest::class, $record);
                return Response::generateSuccess();
            })
            ->performExcel();
    }
}
