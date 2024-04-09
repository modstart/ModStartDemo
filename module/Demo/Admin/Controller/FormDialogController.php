<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Widget\Box;
use ModStart\Widget\ButtonDialogRequest;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FormDialogController extends Controller
{
    use DemoPreviewTrait;

    public function dialog(AdminDialogPage $page)
    {
        $form = Form::make();
        $form->text('name', '姓名')->required();
        $form->text('email', '邮箱')->required();
        $form->text('phone', '电话')->required();
        $form->item([
            'name' => '张三',
            'email' => 'zhangsan@qq.com',
            'phone' => '13000000000',
        ])->fillFields();
        $form->showSubmit(false)->showReset(false);
        return $page->body(Box::make($form, '表单'))
            ->pageTitle('默认数据表单')
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generate(
                    0,
                    '保存成功:' . SerializeUtil::jsonEncode($data),
                    null,
                    CRUDUtil::jsDialogClose()
                );
            });
    }

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('点击按钮弹出表单弹窗');
        return $page->pageTitle('弹窗表单')
            ->append(
                Box::make(
                    ButtonDialogRequest::primary('弹出表单', modstart_admin_url('demo/form_dialog/dialog')),
                    '弹窗表单'
                )
            );
    }
}
