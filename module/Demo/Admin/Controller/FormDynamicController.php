<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FormDynamicController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('根据表单的选项展示不同的内容');
        $form = Form::make();
        $form->text('name', '姓名')->required();
        $form->radio('type', '类型')
            ->options([
                1 => '类型1',
                2 => '类型2',
                3 => '类型3',
            ])
            ->defaultValue(1)
            ->when('=', 1, function (Form $form) {
                $form->text('type1', '类型1')->required();
            })
            ->when('=', 2, function (Form $form) {
                $form->richHtml('type2', '类型2')->required();
                $form->image('type2_image', '类型2图片')->required();
            })
            ->when('=', 3, function (Form $form) {
                $form->image('type3', '类型3')->required();
            });
        return $page->body(Box::make($form, '表单'))
            ->pageTitle('表单动态显示')
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generateSuccess('保存成功:' . SerializeUtil::jsonEncode($data));
            });
    }
}
