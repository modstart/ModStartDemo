<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FrontVueController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupDemoPreview('页面可以很方便的集成 Vue 方便开发复杂页面', [
            'codes' => [
                [
                    'name' => 'Blade文件',
                    'type' => 'html',
                    'path' => 'module/Demo/View/admin/front/vue.blade.php',
                ]
            ]
        ]);
        $page->view('module::Demo.View.admin.front.vue');
        return $page->pageTitle('Vue+ElementUI 集成');
    }
}
