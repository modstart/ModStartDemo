<?php

namespace Module\Demo\Web\Controller;


use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Auth\MemberUser;
use Module\Member\Util\UrlUtil;

class DemoController extends ModuleBaseController
{
    public function index()
    {
        return $this->view('demo.index');
    }

    public function memberLoginRequired()
    {
        if (MemberUser::isNotLogin()) {
            return Response::redirect(UrlUtil::login());
        }
        return $this->view('demo.memberLoginRequired');
    }
}
