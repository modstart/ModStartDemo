<?php



$router->match(['get', 'post'], 'demo', 'DemoController@index');
$router->match(['get', 'post'], 'demo/news/{id}', 'DemoNewsController@show');
