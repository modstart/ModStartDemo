<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoTest;
use Module\Demo\Model\DemoTestCategory;

class GridOperateController extends Controller
{
    use DemoPreviewTrait;
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $this->setupDemoPreview('使用快速 CRUD 的方法，可以自定义一些操作');
        $builder
            ->init(DemoTest::class)
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->select('categoryId', '分类')->optionModelTree(DemoTestCategory::class);
                $builder->text('title', '名称')->asLink(modstart_web_url('demo/test/{id}'));
                $builder->image('cover', '封面');
                $builder->textarea('summary', '摘要')->listable(false);
                $builder->richHtml('content', '内容')->listable(false);
                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->canBatchDelete(true)
            ->canBatchSelect(true)
            ->batchOperatePrepend(
                join('', [
                    '<button class="btn" data-batch-dialog-operate="' . modstart_admin_url('demo/grid_operate/batch_edit') . '"><i class="iconfont icon-ms"></i> 批量编辑</button>',
                ])
            )
            ->footOperate(
                join('', [
                    '<button class="btn" data-batch-dialog-operate="' . modstart_admin_url('demo/grid_operate/batch_edit') . '"><i class="iconfont icon-ms"></i> 批量编辑</button>',
                ])
            )
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('title', L('Title'));
            })
            ->title('默认数据表格');
    }

    public function batchEdit(AdminDialogPage $dialog)
    {
        $ids = CRUDUtil::ids();
        $form = Form::make('');
        $form->display('id', '批量操作的ID')
            ->addable(true)
            ->value('选择' . count($ids) . '个ID:' . join(',', $ids));
        $form->text('title', '标题')->required();
        $form->showReset(false)->showSubmit(false);
        return $dialog->pageTitle('批量操作')
            ->body($form)
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                $text = SerializeUtil::jsonEncode($data);
                return Response::generateSuccess('批量修改成功:' . $text);
            });
    }
}
