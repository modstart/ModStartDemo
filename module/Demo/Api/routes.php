<?php

$router->group([
    'middleware' => [
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'demo/news/get', 'NewsController@get');
    $router->match(['get', 'post'], 'demo/news/paginate', 'NewsController@paginate');

    $router->match(['get', 'post'], 'demo/news_category/all', 'NewsCategoryController@all');

});
