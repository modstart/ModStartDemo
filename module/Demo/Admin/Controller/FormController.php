<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FormController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('快速创建一个配置表单页面，保存支持自定义处理');
        $form = Form::make();
        $form->text('name', '姓名')->required();
        $form->text('email', '邮箱')->required();
        $form->text('phone', '电话')->required();
        $form->item([
            'name' => '张三',
            'email' => 'zhangsan@qq.com',
            'phone' => '13000000000',
        ])->fillFields();
        return $page->body(Box::make($form, '表单'))
            ->pageTitle('默认数据表单')
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generateSuccess('保存成功:' . SerializeUtil::jsonEncode($data));
            });
    }
}
