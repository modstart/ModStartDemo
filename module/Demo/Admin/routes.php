<?php



$router->match(['get', 'post'], 'demo/grid_tree', 'GridTreeController@index');
$router->match(['get', 'post'], 'demo/grid_tree/add', 'GridTreeController@add');
$router->match(['get', 'post'], 'demo/grid_tree/edit', 'GridTreeController@edit');
$router->match(['post'], 'demo/grid_tree/delete', 'GridTreeController@delete');
$router->match(['get'], 'demo/grid_tree/show', 'GridTreeController@show');
$router->match(['post'], 'demo/grid_tree/sort', 'GridTreeController@sort');

$router->match(['get', 'post'], 'demo/grid', 'GridController@index');
$router->match(['get', 'post'], 'demo/grid/add', 'GridController@add');
$router->match(['get', 'post'], 'demo/grid/edit', 'GridController@edit');
$router->match(['post'], 'demo/grid/delete', 'GridController@delete');
$router->match(['get'], 'demo/grid/show', 'GridController@show');
$router->match(['get', 'post'], 'demo/grid/import', 'GridController@import');

$router->match(['get', 'post'], 'demo/grid_operate', 'GridOperateController@index');
$router->match(['get', 'post'], 'demo/grid_operate/add', 'GridOperateController@add');
$router->match(['get', 'post'], 'demo/grid_operate/edit', 'GridOperateController@edit');
$router->match(['post'], 'demo/grid_operate/delete', 'GridOperateController@delete');
$router->match(['get'], 'demo/grid_operate/show', 'GridOperateController@show');
$router->match(['get', 'post'], 'demo/grid_operate/batch_edit', 'GridOperateController@batchEdit');

$router->match(['get', 'post'], 'demo/grid_raw', 'GridRawController@index');
$router->match(['get', 'post'], 'demo/grid_raw/add', 'GridRawController@add');
$router->match(['get', 'post'], 'demo/grid_raw/edit', 'GridRawController@edit');
$router->match(['get'], 'demo/grid_raw/show', 'GridRawController@show');
$router->match(['post'], 'demo/grid_raw/delete', 'GridRawController@delete');

$router->match(['get', 'post'], 'demo/grid_multi', 'GridMultiController@index');

$router->match(['get', 'post'], 'demo/grid_custom_item', 'GridCustomItemController@index');

$router->match(['get', 'post'], 'demo/form', 'FormController@index');
$router->match(['get', 'post'], 'demo/form_config', 'FormConfigController@index');
$router->match(['get', 'post'], 'demo/form_layout', 'FormLayoutController@index');
$router->match(['get', 'post'], 'demo/form_field', 'FormFieldController@index');
$router->match(['get', 'post'], 'demo/form_field/server/{type}', 'FormFieldController@server');
$router->match(['get', 'post'], 'demo/form_dynamic', 'FormDynamicController@index');
$router->match(['get', 'post'], 'demo/form_dialog', 'FormDialogController@index');
$router->match(['get', 'post'], 'demo/form_dialog/dialog', 'FormDialogController@dialog');

$router->match(['get', 'post'], 'demo/widget', 'WidgetController@index');
$router->match(['get', 'post'], 'demo/widget_static', 'WidgetStaticController@index');
$router->match(['get', 'post'], 'demo/widget_vue', 'WidgetVueController@index');

$router->match(['get', 'post'], 'demo/front_basic', 'FrontBasicController@index');
$router->match(['get', 'post'], 'demo/front_icon', 'FrontIconController@index');
$router->match(['get', 'post'], 'demo/front_tw', 'FrontTwController@index');
$router->match(['get', 'post'], 'demo/front_layout', 'FrontLayoutController@index');
$router->match(['get', 'post'], 'demo/front_vue', 'FrontVueController@index');
$router->match(['get', 'post'], 'demo/front_echart', 'FrontEchartController@index');

$router->match(['get', 'post'], 'demo/front_page/404', 'FrontPageController@page404');
$router->match(['get', 'post'], 'demo/front_page/500', 'FrontPageController@page500');

$router->match(['get', 'post'], 'demo/app_import', 'AppImportController@index');
$router->match(['get', 'post'], 'demo/app_test_job', 'AppTestJobController@index');
$router->match(['get'], 'demo/app_test_job/show', 'AppTestJobController@show');
$router->match(['post'], 'demo/app_test_job/create', 'AppTestJobController@create');

$router->match(['get', 'post'], 'demo/test_job', 'AppImportController@index');

$router->match(['get', 'post'], 'demo/doc', 'DocController@index');

