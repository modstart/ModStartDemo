<?php


namespace Module\Demo\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FormConfigController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminConfigBuilder $builder)
    {
        $this->setupDemoPreview('初始时会从 config 表读取，保存时可以写入 config 表，可以使用 modstart_config 获取配置项');
        $builder->pageTitle('系统配置表单');
        $builder->text('System_SiteName', '网站名称')->required();
        $builder->text('System_SiteUrl', '网站地址')->required();
        $builder->text('System_SiteEmail', '网站邮箱')->required();
        $builder->formClass('wide');
        return $builder->perform();
    }
}
