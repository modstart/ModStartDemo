<?php

namespace Module\Demo\Admin\Traits;

use ModStart\ModStart;

trait DemoPreviewTrait
{
    protected function setupHighlightCode()
    {
        ModStart::js('vendor/Demo/js/prism/prism.js');
        ModStart::css('vendor/Demo/js/prism/prism.css');
        ModStart::script('Prism.highlightAll();');
    }

    protected function setupDemoPreview($description, $param = [])
    {
        $param = array_merge([
            'codes' => [],
        ], $param);
        ModStart::js('vendor/Demo/js/prism/prism.js');
        ModStart::css('vendor/Demo/js/prism/prism.css');
        $codes = [];

        $ref = new \ReflectionClass(__CLASS__);
        $file = $ref->getFileName();
        $contentHtml = htmlspecialchars(file_get_contents($file));
        $codes[] = [
            'name' => '调用代码',
            'type' => 'php',
            'contentHtml' => $contentHtml,
            'path' => substr($file, strlen(base_path()) + 1),
        ];

        if (!empty($param['codes'])) {
            foreach ($param['codes'] as $code) {
                $contentHtml = htmlspecialchars(file_get_contents(base_path($code['path'])));
                $codes[] = [
                    'name' => $code['name'],
                    'type' => $code['type'],
                    'contentHtml' => $contentHtml,
                    'path' => $code['path'],
                ];
            }
        }

        $html = [];
        $html[] = '<div>';
        $html[] = '<div class="ub-content-box tw-shadow-lg ub-alert warning tw-fixed tw-left-3 tw-right-3 tw-bottom-3 tw-flex tw-items-center">';
        $html[] = '<div class="tw-flex-grow"><div class="tw-font-bold">' . $description . '</div></div>';
        $html[] = '<div class="tw-mr-1"><a href="javascript:;" class="btn btn-round" id="demoPreviewCode"><i class="iconfont icon-code"></i> 查看代码</a></div>';
        $html[] = '<div class="tw-mr-1"><a href="javascript:;" class="btn btn-round" onclick="$(this).parent().parent().parent().remove();"><i class="iconfont icon-close"></i></a></div>';
        $html[] = '</div>';
        $html[] = '<div class="tw-h-20"></div>';
        $html[] = '</div>';

        $dialogContent = [];
        $dialogContent[] = '<div>';
        $dialogContent[] = '<div data-demo-preview-code-tab class="margin-bottom">';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<a href='javascript:;' class='btn btn-round " . (0 === $codeIndex ? 'btn-primary' : '') . " tw-mr-1'><i class='iconfont icon-code'></i> {$code['name']}</a>";
        }
        $dialogContent[] = '</div>';
        $dialogContent[] = '<div data-demo-preview-code-path>';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<div class='ub-alert tw-font-mono' style='display:" . (0 === $codeIndex ? 'block' : 'none') . ";' data-demo-preview-code-path-item>路径：{$code['path']}</div>";
        }
        $dialogContent[] = '</div>';
        $dialogContent[] = '<div data-demo-preview-code-content>';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<div style='display:" . (0 === $codeIndex ? 'block' : 'none') . ";' data-demo-preview-code-content-item><pre style='height:calc(100vh - 8rem);width:calc(100vw - 6rem);overflow-y:auto;' class='language-{$code['type']} line-numbers'><code>{$code['contentHtml']}</code></pre></div>";
        }
        $dialogContent[] = '</div>';
        $dialogContent[] = '</div>';

        $dialogContent = json_encode(join('', $dialogContent));

        $html = json_encode(join('', $html));
        $js = <<<JS
$('body').append($html)
$('#demoPreviewCode').on('click',function(){
    MS.dialog.dialogContent('<div class="ub-content-box" id="demoPreviewCodeDialog">'+{$dialogContent}+'</div>',{
        openCallback:function(){
            Prism.highlightAllUnder(document.getElementById('demoPreviewCodeDialog'));
        }
    });
    return false;
});
$(document).on('click','[data-demo-preview-code-tab] a',function(){
    var index = $(this).index();
    $(this).addClass('btn-primary').siblings().removeClass('btn-primary');
    $('[data-demo-preview-code-path] [data-demo-preview-code-path-item]').eq(index).show().siblings().hide();
    $('[data-demo-preview-code-content] [data-demo-preview-code-content-item]').eq(index).show().siblings().hide();
});
JS;
        ModStart::script($js);
    }
}
