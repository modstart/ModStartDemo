<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Response;
use ModStart\Widget\Box;

class DocController extends Controller
{
    public function index(AdminPage $page)
    {
        $html = <<<HTML
<div class="ub-alert tw-font-bold">ModStart 是一个后台开发框架，非常适合全栈开发，提高后台管理开发效率。</div>
<div class="ub-alert tw-font-bold warning">ModStart 又不止是一个后台开发框架，提供了完善的用户前台开发规范，支持 Web、Api 快速开发。</div>
<div class="ub-alert tw-font-bold success">ModStart 拥有一整套的开发生态，无论是软件外包还是自有产品，都能轻松应对。</div>
<div class="ub-alert tw-font-bold danger">我们提供了详细的开发文档，点击立即开始使用吧 ^_^</div>
<div>
    <a href="https://modstart.com/" target="_blank" class="btn btn-round"><i class="iconfont icon-home"></i> 访问官网</a>
    <a href="https://modstart.com/store" target="_blank" class="btn btn-round"><i class="iconfont icon-cube"></i> 模块市场</a>
    <a href="https://modstart.com/doc" target="_blank" class="btn btn-round"><i class="iconfont icon-book"></i> 开发文档</a>
</div>
<p>

</p>
HTML;

        $box = Box::make($html, '开发者文档');
        return $page->pageTitle('开发者文档')
            ->body($box);
    }
}
