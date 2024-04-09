<?php

namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Util\RenderUtil;
use ModStart\ModStart;
use ModStart\Widget\Box;
use Module\Demo\Admin\Traits\DemoPreviewTrait;

class FrontIconController extends Controller
{
    use DemoPreviewTrait;

    private function getSystemIcons()
    {
        $records = [];

        $file = base_path('vendor/modstart/modstart/asset/vendor/iconfont/iconfont.json');
        $icons = [];
        if (file_exists($file)) {
            $fileIcons = @json_decode(file_get_contents($file), true);
            $icons = array_map(function ($item) {
                return [
                    'title' => $item['name'],
                    'cls' => 'iconfont icon-' . $item['font_class'],
                ];
            }, $fileIcons['glyphs']);
        }
        $records[] = [
            'title' => '图标 · 基础',
            'icons' => $icons,
        ];

        $icons = [];
        if (file_exists($file = public_path('asset/font-awesome/css/font-awesome.min.css')) && ($content = file_get_contents($file))) {
            preg_match_all('/\\.fa-([a-z0-9\\-]+):before/', $content, $mat);
            $icons = array_map(function ($title) {
                return [
                    'title' => $title,
                    'cls' => "fa fa-$title",
                ];
            }, $mat[1]);
        }
        $records[] = [
            'title' => '图标 · Font Awesome',
            'icons' => $icons,
        ];
        return $records;
    }

    public function index(AdminPage $page)
    {
        $this->setupHighlightCode();

        $remark = <<<REMARK
<p>直接通过设置类名为 iconfont icon-list 来使用即可。例如：</p>
<p><pre class="language-html"><code>&lt;i class="iconfont icon-home"&gt;&lt;/i&gt;</code></pre></p>
<p>效果为</p>
<p><i class="iconfont icon-home"></i></p>
REMARK;
        $boxRemark = Box::make($remark, '使用说明');

        ModStart::js('asset/common/clipboard.js');
        $iconHtml = RenderUtil::view('module::Demo.View.admin.front.icon', [
            'records' => $this->getSystemIcons(),
        ]);

        return $page
            ->pageTitle('Icon 图标')
            ->append($boxRemark)
            ->append($iconHtml);
    }
}
