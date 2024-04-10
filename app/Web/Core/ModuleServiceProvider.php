<?php


namespace App\Web\Core;


use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use ModStart\Core\Hook\ModStartHook;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        ModStartHook::subscribe('PageHeadAppend', function () {
            return modstart_config('DemoHeadAppend', '');
        });
        ModStartHook::subscribe('AdminPageHeadAppend', function () {
            return modstart_config('DemoHeadAppend', '');
        });
        ModStartHook::subscribe('AdminLoginBodyAppend', function () {
            return modstart_config('DemoAdminLoginBodyAppend', '');
        });
    }

    
    public function register()
    {

    }
}
