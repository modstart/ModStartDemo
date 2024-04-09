<?php

use Illuminate\Support\Facades\Route;

$middlewares = [
    'web.bootstrap'
];
if (class_exists(\Module\Member\Middleware\WebAuthMiddleware::class)) {
    $middlewares[] = \Module\Member\Middleware\WebAuthMiddleware::class;
}

Route::group(
    [
        'namespace' => '\App\Web\Controller',
        'middleware' => $middlewares,
    ], function () {

    Route::match(['get', 'post'], '', 'IndexController@index');
    Route::match(['get', 'post'], 'member/{id}', 'MemberController@show');
    Route::match(['get', 'post'], 'member_profile', 'MemberProfileController@index');

});


