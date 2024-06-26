<?php


namespace ModStart\Admin\Layout;


use Illuminate\Contracts\Support\Renderable;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\IdUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Field\AbstractField;
use ModStart\Form\Form;
use ModStart\Layout\Page;
use ModStart\Repository\RepositoryUtil;
use ModStart\Widget\Box;

/**
 * Class AdminConfigBuilder
 * @package ModStart\Admin\Layout
 *
 * @mixin Page
 * @mixin Form
 * @method $this disableBoxWrap($disable)
 * @method $this hookFormWrap($callback)
 */
class AdminConfigBuilder implements Renderable
{
    /** @var Page */
    private $page;
    /** @var Form */
    private $form;
    private $pagePrepend = [];
    private $pageAppend = [];
    private $config = [
        'disableBoxWrap' => false,
        'hookFormWrap' => null,
    ];

    public function __construct()
    {
        $this->form = new Form(DynamicModel::class);
        $this->useFrame();
    }

    public function form()
    {
        return $this->form;
    }

    public function page()
    {
        return $this->page;
    }

    public function useFrame()
    {
        $this->page = new AdminPage();
        $this->form->showReset(false)->showSubmit(true);
    }

    public function useDialog()
    {
        $this->page = new AdminDialogPage();
        $this->form->showReset(false)->showSubmit(false);
    }

    public function pagePrepend($widget)
    {
        array_unshift($this->pagePrepend, $widget);
    }

    public function pageAppend($widget)
    {
        $this->pageAppend[] = $widget;
    }

    public function render()
    {
        if (!empty($this->pagePrepend)) {
            foreach ($this->pagePrepend as $item) {
                $this->page->row($item);
            }
        }
        $body = $this->form;
        if ($this->config['hookFormWrap']) {
            $body = call_user_func($this->config['hookFormWrap'], $body);
        } else if (!$this->config['disableBoxWrap']) {
            $body = new Box($this->form, $this->page->pageTitle());
        }
        $this->page->body($body);
        if (!empty($this->pageAppend)) {
            foreach ($this->pageAppend as $item) {
                $this->page->row($item);
            }
        }
        return $this->page->render();
    }

    /**
     * @param $item \stdClass|null|false 表示使用默认的 modstart_config 配置获取，false 表示不使用任何内容初始化
     * @param $callback \Closure = function (Form $form) { return Response::generateSuccess('ok'); }
     * @param $callbackPreCheck \Closure = function (Form $form) { BizException::throws('error') }
     * @return $this
     */
    public function perform($item = null, $callback = null, $callbackPreCheck = null)
    {
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $this->form->formRequest(function (Form $form) use ($callback, $callbackPreCheck) {
                if ($callbackPreCheck) {
                    call_user_func($callbackPreCheck, $form);
                }
                if ($callback) {
                    $ret = call_user_func($callback, $form);
                    if (null !== $ret) {
                        return $ret;
                    }
                } else {
                    $config = modstart_config();
                    foreach ($form->dataForming() as $k => $v) {
                        $config->set($k, $v);
                    }
                }
                return Response::jsonSuccess(L('Save Success'));
            });
        }
        if (null === $item) {
            $item = [];
            $config = modstart_config();
            foreach ($this->form->fields() as $field) {
                /** @var $field AbstractField */
                if ($field->isLayoutField()) {
                    continue;
                }
                $hasValue = $config->has($field->column());
                if ($hasValue) {
                    $v = modstart_config($field->column());
                } else {
                    $v = null;
                }
                if (is_array($v)) {
                    $v = SerializeUtil::jsonEncode($v);
                }
                $item[$field->column()] = $v;
            }
        } else if (false === $item) {
            $item = [];
            foreach ($this->form->fields() as $field) {
                /** @var $field AbstractField */
                if ($field->isLayoutField()) {
                    continue;
                }
                $item[$field->column()] = null;
            }
        }
        $this->form->item(RepositoryUtil::itemFromArray($item));
        $this->form->fillFields();
        return $this;
    }

    public function contentFixedBottomContentSave()
    {
        $this->contentFixedBottomContent('<button type="submit" class="btn btn-primary">' . L('Save') . '</button>');
    }

    public function contentFixedBottomContent($html)
    {
        $id = IdUtil::generate('ContentFixedBottomContent');
        $this->layoutHtml('
<div class="content-fixed-bottom-toolbox-placeholder" id="' . $id . 'Placeholder"></div>
<div class="content-fixed-bottom-toolbox" id="' . $id . 'Content">' . $html . '</div>
<script>$(function(){ $("#' . $id . 'Placeholder").css("height",20+$("#' . $id . 'Content").height()+"px"); });</script>
');
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, $this->config)) {
            $this->config[$name] = $arguments[0];
            return $this;
        }
        if (method_exists($this->page, $name)) {
            return $this->page->{$name}(...$arguments);
        }
        return $this->form->{$name}(...$arguments);
    }


}
