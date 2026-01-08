<?php

$middleware = [];
if (class_exists(\Module\Member\Middleware\ApiAuthMiddleware::class)) {
    $middleware[] = \Module\Member\Middleware\ApiAuthMiddleware::class;
}
$router->group([
    'middleware' => $middleware,
], function () use ($router) {

    $router->match(['get', 'post'], 'demo/news/get', 'NewsController@get');
    $router->match(['get', 'post'], 'demo/news/paginate', 'NewsController@paginate');

    $router->match(['get', 'post'], 'demo/news_category/all', 'NewsCategoryController@all');

});
