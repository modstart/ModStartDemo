<?php


$middleware = [];
if (class_exists(\Module\Member\Middleware\WebAuthMiddleware::class)) {
    $middleware[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}
$router->group([
    'middleware' => $middleware,
], function () use ($router) {
    $router->match(['get'], 'demo', 'DemoController@index');
    $router->match(['get'], 'demo/member_login_required', 'DemoController@memberLoginRequired');

    $router->match(['get'], 'demo/news/{id}', 'DemoNewsController@show');
});
