<?php


namespace Module\Demo\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;

class FrontPageController extends Controller
{
    public function page404()
    {
        return Response::page404();
    }

    public function page500()
    {
                return view('errors.500');
    }
}
