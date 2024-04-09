<?php
$middlewares = [
    'api.bootstrap'
];
if (class_exists(\Module\Member\Middleware\ApiAuthMiddleware::class)) {
    $middlewares[] = \Module\Member\Middleware\ApiAuthMiddleware::class;
}
Route::group(
    [
        'middleware' => $middlewares,
        'namespace' => '\App\Api\Controller',
        'prefix' => 'api',
    ], function () {

    Route::match(['get', 'post'], 'config_app', 'ConfigController@app');

});
