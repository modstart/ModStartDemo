<?php


namespace Module\AdminManager\Traits;

use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\SecurityTooltipBox;
use ModStart\Layout\Row;
use Module\AdminManager\Widget\ServerInfoWidget;
use Module\Vendor\Admin\Widget\AdminWidgetDashboard;


trait AdminDashboardTrait
{
    public function dashboard($callback = null)
    {
        
        $page = app(AdminPage::class);
        $page->pageTitle(L('Dashboard'));
        $page->row(new SecurityTooltipBox());
        if ($callback) {
            call_user_func($callback, $page);
        }
        $page->append(new Row(function (Row $row) {
            AdminWidgetDashboard::callIcon($row);
        }));
        AdminWidgetDashboard::call($page);
        $page->append(new ServerInfoWidget());
        return $page;
    }
}
