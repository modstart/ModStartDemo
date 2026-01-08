<?php

namespace Module\Demo\Admin\Traits;

use Illuminate\Support\Str;
use ModStart\Core\Util\FileUtil;
use ModStart\ModStart;

trait DemoPreviewTrait
{
    protected function setupHighlightCode()
    {
        ModStart::js('vendor/Demo/js/prism/prism.js');
        ModStart::css('vendor/Demo/js/prism/prism.css');
        ModStart::script('Prism.highlightAll();');
    }

    private function filterCodeContent($content, $type)
    {
        if ('php' == $type) {
            $content = trim($content);
            $lines = explode("\n", $content);
            $blackList = [
                '$this->setupDemoPreview',
                'DemoPreviewTrait',
            ];
            $lines = array_filter($lines, function ($line) use ($blackList) {
                foreach ($blackList as $item) {
                    if (Str::contains($line, $item)) {
                        return false;
                    }
                }
                return true;
            });
            $content = join("\n", $lines);
        }
                $content = str_replace("\r", '', $content);
        $content = preg_replace('/\n{2,}/', "\n\n", $content);
        return $content;
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
        $codeContent = file_get_contents($file);
        $ext = FileUtil::extension($file);
        $codeContent = $this->filterCodeContent($codeContent, $ext);
        $contentHtml = htmlspecialchars($codeContent);
        $codes[] = [
            'name' => '代码',
            'type' => 'php',
            'contentHtml' => $contentHtml,
            'path' => substr($file, strlen(base_path()) + 1),
        ];

        if (!empty($param['codes'])) {
            foreach ($param['codes'] as $code) {
                $file = file_get_contents(base_path($code['path']));
                $ext = FileUtil::extension($code['path']);
                $codeContent = $this->filterCodeContent($file, $ext);
                $contentHtml = htmlspecialchars($codeContent);
                $codes[] = [
                    'name' => $code['name'],
                    'type' => $code['type'],
                    'contentHtml' => $contentHtml,
                    'path' => $code['path'],
                ];
            }
        }

        $html = [];
        $html[] = '<div class="pb-demo-container">';
        $html[] = '<div class="ub-content-box tw-shadow-lg ub-alert warning tw-fixed tw-left-3 tw-right-3 tw-bottom-3 tw-flex tw-items-center">';
        $html[] = '<div class="tw-flex-grow"><div class="tw-font-bold">' . $description . '</div></div>';
        $html[] = '<div class="tw-mr-1"><a href="javascript:;" class="btn btn-round" id="demoPreviewCode"><i class="iconfont icon-code"></i> 查看代码</a></div>';
        $html[] = '<div class="tw-mr-1"><a href="javascript:;" class="btn btn-round" onclick="$(this).parent().parent().parent().remove();"><i class="iconfont icon-close"></i></a></div>';
        $html[] = '</div>';
        $html[] = '<div class="tw-h-20"></div>';
        $html[] = '</div>';

        $css = <<<CSS
[data-demo-preview-dialog]{
    width:calc(100vw - 3rem);
    height:calc(100vh - 3rem);
}
@media (max-width:40rem) {
    [data-demo-preview-dialog]{
        width:calc(100vw - 3rem);
        height:calc(100vh - 3rem);
    }
}
CSS;

        ModStart::style($css);


        $dialogContent = [];
        $dialogContent[] = '<div data-demo-preview-dialog>';
        $dialogContent[] = '<div data-demo-preview-code-tab class="margin-bottom">';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<a href='javascript:;' class='btn btn-round " . (0 === $codeIndex ? 'btn-primary' : '') . " tw-mr-1'><i class='iconfont icon-code'></i> {$code['name']}</a>";
        }
        $dialogContent[] = '</div>';
        $dialogContent[] = '<div data-demo-preview-code-path>';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<div class='ub-alert tw-font-mono' style='word-break:break-all;display:" . (0 === $codeIndex ? 'block' : 'none') . ";' data-demo-preview-code-path-item>路径：{$code['path']}</div>";
        }
        $dialogContent[] = '</div>';
        $dialogContent[] = '<div data-demo-preview-code-content>';
        foreach ($codes as $codeIndex => $code) {
            $dialogContent[] = "<div style='display:" . (0 === $codeIndex ? 'block' : 'none') . ";' data-demo-preview-code-content-item><pre style='overflow:auto;' class='language-{$code['type']} line-numbers'><code>{$code['contentHtml']}</code></pre></div>";
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
if($('.ub-panel-dialog').length>0){
    $('.pb-demo-container .ub-content-box').css({bottom:'2.5rem'});
}
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
