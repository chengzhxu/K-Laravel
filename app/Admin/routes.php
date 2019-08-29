<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/kevin', 'kevinController@list')->name('kevin.list');
    $router->get('/kevin/create', 'kevinController@create')->name('kevin.create');
    $router->post('/kevin/save', 'kevinController@save')->name('kevin.save');

});
