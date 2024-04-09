<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Form\Form;
use ModStart\Layout\LayoutGrid;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Type\DemoType;

class FormFieldController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('这个表单列出了所有可支持的组件');

        $form = Form::make('');

        $form->layoutGrid(function (LayoutGrid $layout) {
            $layout->layoutColumn(6, function (Form $form) {
                $form->text('text', 'text');
                $form->textarea('textarea', 'textarea');
                $form->password('password', 'password');
                $form->number('number', 'number');
                $form->decimal('decimal', 'decimal');
                $form->currency('currency', 'currency');
                $form->date('date', 'date');
                $form->datetime('datetime', 'datetime');
                $form->time('time', 'time');
                $form->switch('switch', 'switch');
                $form->radio('radio', 'radio')->optionType(DemoType::class);
                $form->checkbox('checkbox', 'checkbox')->optionType(DemoType::class);
                $form->select('select', 'select')->optionType(DemoType::class);
                $form->type('type', 'type')->type(DemoType::class);
                $form->tags('tags', 'tags');
                $form->percent('percent', 'percent');
                $form->numberRange('numberRange', 'numberRange');
                $form->link('link', 'link');
                $form->button('button', 'button');
                $form->html('html', 'html')->html('html');
                $form->multiSelect('multiSelect', 'multiSelect')->optionType(DemoType::class);
                $form->period('period', 'period');
                $form->selectRemote('selectRemote', 'selectRemote');
            });
            $layout->layoutColumn(6, function (Form $form) {
                $form->color('color', 'color');
                $form->image('image', 'image');
                $form->images('images', 'images');
                $form->imagesTemp('imagesTemp', 'imagesTemp');
                $form->file('file', 'file');
                $form->fileTemp('fileTemp', 'fileTemp');
                $form->files('files', 'files');
                $form->audio('audio', 'audio');
                $form->video('video', 'video');
                $form->code('code', 'code');
                $form->json('json', 'json');
                $form->values('values', 'values');
                $form->rate('rate', 'rate');
                $form->icon('icon', 'icon');
                $form->captcha('captcha', 'captcha')->url(modstart_admin_url('login/captcha'));
                $form->jsonIdItems('jsonIdItems', 'jsonIdItems');
                $form->keyValueList('keyValueList', 'keyValueList');
                $form->tree('tree', 'tree');
            });
            $layout->layoutColumn(12, function (Form $form) {
                $form->richHtml('richHtml', 'richHtml');
                $form->markdown('markdown', 'markdown');
                $form->complexFields('complexFields', 'complexFields')->fields([
                    ['name' => 'xxx1', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'tip' => 'xxx',],
                    ['name' => 'xxx2', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'tip' => 'xxx',],
                    ['name' => 'xxx3', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'tip' => 'xxx',],
                    ['name' => 'xxx4', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'tip' => 'xxx',],
                    ['name' => 'xxx5', 'title' => '数字', 'type' => 'slider', 'defaultValue' => 0, 'min' => 1, 'max' => 5, 'step' => 1, 'tip' => 'xxx',],
                    ['name' => 'xxx6', 'title' => '链接', 'type' => 'link', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                ]);
                $form->complexFieldsList('complexFieldsList', 'complexFieldsList')->fields([
                    ['name' => 'xxx1', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'tip' => 'xxx',],
                    ['name' => 'xxx2', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'tip' => 'xxx',],
                    ['name' => 'xxx3', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'tip' => 'xxx',],
                    ['name' => 'xxx4', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'tip' => 'xxx',],
                    ['name' => 'xxx5', 'title' => '链接', 'type' => 'link', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
                ]);
            });
        });

        return $page->pageTitle('表单所有组件')
            ->body(Box::make($form, '表单所有组件'))
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generateSuccess('保存成功:' . SerializeUtil::jsonEncode($data));
            });
    }
}
