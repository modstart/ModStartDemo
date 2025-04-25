<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\TreeUtil;
use ModStart\Field\Json;
use ModStart\Field\SelectRemote;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;
use Module\Demo\Model\DemoNews;
use Module\Demo\Model\DemoNewsCategory;
use Module\Demo\Type\DemoType;

class FormFieldController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('这个表单列出了所有可支持的组件');

        $form = Form::make('');

        $form->text('text', '单行文本');
        $form->textarea('textarea', '多行文本');
        $form->password('password', '密码');
        $form->number('number', '单行数字');
        $form->decimal('decimal', '单行小数');
        $form->currency('currency', '单行货币');
        $form->date('date', '日期');
        $form->datetime('datetime', '日期时间');
        $form->time('time', '时间');
        $form->switch('switch', '开关');
        $form->radio('radio', '单选')->optionType(DemoType::class);
        $form->checkbox('checkbox', '复选')->optionType(DemoType::class);
        $form->select('select', '下拉')->optionType(DemoType::class);
        $form->type('type', '类型')->type(DemoType::class);
        $form->tags('tags', '标签');
        $form->percent('percent', '单行百分比');
        $form->numberRange('numberRange', '单行数字范围');
        $form->link('link', '链接');
        $form->button('button', '按钮');
        $form->html('html', 'HTML')->html('<div style="background:#F8F8F8;border-radius:1rem;padding:1rem;" class="ub-html"><p>HTML内容</p><p>HTML内容</p></div>');
        $form->multiSelect('multiSelect', '多选')->optionType(DemoType::class);
        $form->period('period', '时间长度');
        $form->selectRemote('selectRemote', '远程下拉')->server(modstart_admin_url('demo/form_field/server/selectRemote'));
        $form->areaChina('areaChina', '中国地区');
        $form->color('color', '颜色');
        $form->image('image', '单图');
        $form->images('images', '多图');
        $form->imagesTemp('imagesTemp', '多图（临时）');
        $form->file('file', '单文件');
        $form->files('files', '多文件');
        $form->fileTemp('fileTemp', '单文件（临时）');
        $form->audio('audio', '单音频');
        $form->video('video', '单视频');
        $form->code('code', '代码');
        $form->json('json', 'JSON');
        $form->json('jsonApi', '接口请求')->jsonMode(Json::MODE_API);
        $form->values('values', '值列表');
        $form->rate('rate', '评分');
        $form->icon('icon', '图标');
        $form->captcha('captcha', '验证码')->url(modstart_admin_url('login/captcha'));
        $form->jsonIdItems('jsonIdItems', 'JSON ID列表')
            ->selectUrl(modstart_admin_url('demo/form_field/server/jsonIdItemsSelect'))
            ->previewUrl(modstart_admin_url('demo/form_field/server/jsonIdItemsPreview'));
        $form->keyValueList('keyValueList', '键值对列表');
        $form->tree('tree', '树')
            ->tree(TreeUtil::modelToTree(DemoNewsCategory::class, ['title' => 'title']));
        $form->richHtml('richHtml', '富文本');
        $form->markdown('markdown', 'Markdown');
        $form->complexFields('complexFields', '复杂字段组')->fields([
            ['name' => 'xxx1', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'tip' => 'xxx',],
            ['name' => 'xxx2', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'tip' => 'xxx',],
            ['name' => 'xxx3', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'tip' => 'xxx',],
            ['name' => 'xxx4', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'tip' => 'xxx',],
            ['name' => 'xxx5', 'title' => '数字', 'type' => 'slider', 'defaultValue' => 0, 'min' => 1, 'max' => 5, 'step' => 1, 'tip' => 'xxx',],
            ['name' => 'xxx6', 'title' => '链接', 'type' => 'link', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
        ]);
        $form->complexFieldsList('complexFieldsList', '复杂字段组列表')->fields([
            ['name' => 'xxx1', 'title' => '开关', 'type' => 'switch', 'defaultValue' => false, 'tip' => 'xxx',],
            ['name' => 'xxx2', 'title' => '文本', 'type' => 'text', 'defaultValue' => '', 'tip' => 'xxx',],
            ['name' => 'xxx3', 'title' => '图标', 'type' => 'icon', 'defaultValue' => 'iconfont icon-home', 'tip' => 'xxx',],
            ['name' => 'xxx4', 'title' => '数字', 'type' => 'number', 'defaultValue' => 0, 'tip' => 'xxx',],
            ['name' => 'xxx5', 'title' => '链接', 'type' => 'link', 'defaultValue' => '', 'placeholder' => '', 'tip' => '',],
        ]);
        $form->dynamicFields('dynamicFields', '动态字段');
        $form->transfer('transfer', '穿梭框')->optionType(DemoType::class);

        $form->formClass('wide');

        return $page->pageTitle('表单所有组件')
            ->body(Box::make($form, '表单所有组件'))
            ->handleForm($form, function (Form $form) {
                $data = $form->dataForming();
                                return Response::generateSuccess('保存成功:' . '<pre>' . SerializeUtil::jsonEncodePretty($data) . '</pre>');
            });
    }

    public function server(AdminDialogPage $page,
                                           $type)
    {
        switch ($type) {
            case 'selectRemote':
                return SelectRemote::handleModel(DemoNews::class, 'id', 'title');
            case 'jsonIdItemsSelect':
                $grid = Grid::make(DemoNews::class);
                $grid->id('id', 'ID');
                $grid->text('title', '标题');
                $grid->disableCUD();
                $grid->canSingleSelectItem(true);
                return $page->pageTitle('选择用户')->body($grid)->handleGrid($grid);
            case 'jsonIdItemsPreview':
                $records = DemoNews::whereIn('id', CRUDUtil::ids())->get()->toArray();
                return Response::generateSuccessData([
                    'records' => $records,
                ]);
        }
        return Response::generateError('未定义的远程请求');
    }
}
