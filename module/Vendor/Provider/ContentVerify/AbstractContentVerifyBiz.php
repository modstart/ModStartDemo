<?php


namespace Module\Vendor\Provider\ContentVerify;


use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\RenderUtil;
use ModStart\Form\Form;
use Module\Vendor\Provider\CensorImage\CensorImageProvider;
use Module\Vendor\Provider\CensorText\CensorTextProvider;
use Module\Vendor\Provider\Notifier\NotifierProvider;
use Module\Vendor\Util\NoneLoginOperateUtil;

abstract class AbstractContentVerifyBiz
{
    
    abstract public function name();

    
    abstract public function title();

    
    public function verifyAutoProcess($param)
    {
        return false;
    }

    
    public function verifyPassProcess($param)
    {
        BizException::throws('AbstractContentVerifyBiz::verifyPassProcess 未实现');
    }

    
    public function verifyRejectProcess($param)
    {
        BizException::throws('AbstractContentVerifyBiz::verifyRejectProcess 未实现');
    }

    
    abstract public function verifyCount();

    
    abstract public function verifyRule();


    
    public function useFormVerify()
    {
        return false;
    }

    
    public function buildForm(Form $form, $param)
    {
        return null;
    }

    
    public function verifyUrl()
    {
        return action($this->verifyRule());
    }

    
    public function verifyAutoProcessedNotify()
    {
        return true;
    }

    public function run($param, $title = null, $body = null)
    {
        if (null === $body) {
            $body = [
                '内容' => $title,
            ];
            $shortTitle = Str::substr(HtmlUtil::text2html($title), 0, 100);
        } else {
            $shortTitle = $title;
        }
        $shortTitle = HtmlUtil::text($shortTitle);
        $shortTitle = $this->title() . ($shortTitle ? '(' . $shortTitle . ')' : '');
        if ($this->verifyAutoProcess($param)) {
            if ($this->verifyAutoProcessedNotify()) {
                NotifierProvider::notify($this->name(), '[自动审核]' . $shortTitle, $body, $param);
            }
            return;
        }
        NotifierProvider::notifyNoneLoginOperateProcessUrl(
            $this->name(),
            '[审核]' . $shortTitle,
            $body,
            $this->useFormVerify() ? 'content_verify/' . $this->name() : null,
            $param
        );
    }

    protected function parseRichHtml($content)
    {
        $ret = HtmlUtil::extractTextAndImages($content);
        $images = [];
        $text = $ret['text'];
        foreach ($ret['images'] as $image) {
            $images[] = AssetsUtil::fixFull($image);
        }
        return [
            $text, $images
        ];
    }

    protected function censorRichHtmlSuccess($censorProviderKeyPrefix, $content)
    {
        list($text, $images) = $this->parseRichHtml($content);
        if (!empty($text)) {
            $provider = CensorTextProvider::get(modstart_config($censorProviderKeyPrefix . '_Text', 'default'));
            if ($provider) {
                $ret = $provider->verify($text);
                if (Response::isError($ret)) {
                    return false;
                }
                if (!$ret['data']['pass']) {
                    return false;
                }
            }
        }
        if (!empty($images)) {
            $provider = CensorImageProvider::get(modstart_config($censorProviderKeyPrefix . '_Image', 'default'));
            if ($provider) {
                foreach ($images as $image) {
                    $ret = $provider->verify($image);
                    if (Response::isError($ret)) {
                        return false;
                    }
                    if (!$ret['data']['pass']) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function renderAdminAction($param = [])
    {
        if (!Admin::isLogin()) {
            return '';
        }
        $bizer = ContentVerifyBiz::getByName(static::NAME);
        if (null == $bizer) {
            return '';
        }
        if (!AdminPermission::permit($bizer->verifyRule())) {
            return '';
        }
        $passUrl = NoneLoginOperateUtil::generateUrl('content_verify/' . $bizer->name(), array_merge($param, [
            '_action' => 'pass',
        ]));
        $rejectUrl = NoneLoginOperateUtil::generateUrl('content_verify/' . $bizer->name(), array_merge($param, [
            '_action' => 'reject',
        ]));
        return RenderUtil::view('module::Vendor.View.provider.contentVerify.adminAction', [
            'passUrl' => $passUrl,
            'rejectUrl' => $rejectUrl,
        ]);
    }

    public static function callVerifyPassProcess($param)
    {
        $bizer = ContentVerifyBiz::getByName($param['name']);
        BizException::throwsIfEmpty('数据异常', $bizer);
        $bizer->verifyPassProcess($param);
    }

    public static function callVerifyRejectProcess($param)
    {
        $bizer = ContentVerifyBiz::getByName($param['name']);
        BizException::throwsIfEmpty('数据异常', $bizer);
        $bizer->verifyRejectProcess($param);
    }

}
