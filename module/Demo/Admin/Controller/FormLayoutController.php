<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Layout\LayoutGrid;
use ModStart\Layout\LayoutTab;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Type\DemoType;

class FormLayoutController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('这个表单支持复杂布局');

        $form = Form::make('');
        $form->layoutHtml(Box::make('标题文字', '一个单独的文字容器'));

        $form->layoutPanel('内容块1', function (Form $form) {
            $form->switch('Demo_Enable', '按钮布尔值')
                ->when('=', true, function (Form $form) {
                    $form->text('Demo_EnableText', '布尔值联动字段')->help('只有当Demo_Enable为真时显示');
                })
                ->help('推荐命名规则 模块名_键值');
            $form->text('Demo_Title', '文字信息');
            $form->select('Demo_Select', '下拉选项')
                ->optionType(DemoType::class)->help('可以使用实现 <b>BaseType</b> 接口的类');
            $form->radio('Demo_Radio', '下拉选项')
                ->optionType(DemoType::class)->help('可以使用实现 <b>BaseType</b> 接口的类');
        });

        $form->layoutPanel('内容块2', function (Form $form) {
            $form->layoutGrid(function (LayoutGrid $layout) {
                $layout->layoutColumn([6, 12], function (Form $form) {
                    $form->text('Demo_TitleLeft1', '布局左边');
                    $form->text('Demo_TitleLeft2', '布局左边');
                });
                $layout->layoutColumn([6, 12], function (Form $form) {
                    $form->text('Demo_TitleRight1', '布局右边');
                    $form->text('Demo_TitleRight2', '布局右边');
                });
            });
        });

        $form->layoutPanel('内容块3', function (Form $form) {
            $form->layoutTab(function (LayoutTab $layout) {
                $layout->tab('Tab1', function (Form $form) {
                    $form->text('Demo_Tab1Text1', 'Tab1-1内容');
                    $form->text('Demo_Tab1Text2', 'Tab1-2内容');
                });
                $layout->tab('Tab2', function (Form $form) {
                    $form->text('Demo_Tab2Text1', 'Tab2-1内容');
                    $form->text('Demo_Tab2Text2', 'Tab2-2内容');
                });
            });
        });

        $form->layoutHtml('<div class="ub-content-box margin-top margin-bottom"><button type="submit" class="btn btn-primary">保存</button></div>');

        $form->showReset(false)->showSubmit(false);

        return $page->pageTitle('复杂布局表单')
            ->body($form)
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generateSuccess('保存成功:' . SerializeUtil::jsonEncode($data));
            });
    }
}
