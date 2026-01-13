<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FrontBasicController extends Controller
{
    use DemoPreviewTrait;

    public function index(AdminPage $page)
    {
        $this->setupHighlightCode();

        $remark = <<<REMARK
<p>Tailwind CSS 默认已经集成到系统页面中，可以直接使用以下的方式直接使用，前缀为 <code>tw-</code>。例如：</p>
<p><pre class="language-html"><code>&lt;div class="tw-p-3 tw-bg-red-400 tw-text-white tw-rounded tw-shadow-lg"&gt;Hello Tailwind CSS&lt;/div&gt;</code></pre></p>
<p>效果为</p>
<div class="tw-p-3 tw-bg-red-400 tw-text-white tw-rounded tw-shadow-lg">Hello Tailwind CSS</div>
REMARK;
        $boxRemark = Box::make($remark, '使用说明');

        $html = <<<HTML
<p>更多效果可参考 <a href="https://tailwindcss.com/docs" target="_blank">Tailwind CSS 官方文档</a>。</p>
HTML;


        $box = Box::make($html, '使用预览');

        return $page
            ->pageTitle('Tailwind CSS')
            ->append($boxRemark)
            ->append($box);
    }
}
