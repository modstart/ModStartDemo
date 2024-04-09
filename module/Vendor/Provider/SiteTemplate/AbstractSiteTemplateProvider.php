<?php


namespace Module\Vendor\Provider\SiteTemplate;


use ModStart\Form\Form;


abstract class AbstractSiteTemplateProvider
{
    abstract public function name();

    abstract public function title();

    public function root()
    {
        return null;
    }

    
    public function hasConfig()
    {
        return false;
    }

    
    public function config(Form $form, $param = [])
    {

    }
}
