<?php

$middleware = [];
if (class_exists(\Module\Member\Middleware\ApiAuthMiddleware::class)) {
    $middleware[] = \Module\Member\Middleware\ApiAuthMiddleware::class;
}
$router->group([
    'middleware' => $middleware,
], function () use ($router) {

    $router->match(['post'], 'demo/news/get', 'DemoNewsController@get');
    $router->match(['post'], 'demo/news/paginate', 'DemoNewsController@paginate');

    $router->match(['post'], 'demo/news_category/all', 'DemoNewsCategoryController@all');

});
