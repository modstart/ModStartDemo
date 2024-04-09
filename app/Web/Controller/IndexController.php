<?php


namespace App\Web\Controller;

use Module\Vendor\Installer\Util\InstallerUtil;

class IndexController extends BaseController
{
    public function index()
    {
        InstallerUtil::checkForInstallRedirect();
        return $this->view('index');
    }
}
