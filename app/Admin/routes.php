<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/kevin', 'KevinController@list')->name('kevin.list');
    $router->get('/kevin/create', 'KevinController@create')->name('kevin.create');
    $router->post('/kevin/save', 'KevinController@save')->name('kevin.save');
    $router->get('/kevin/{id}', 'KevinController@show')->name('kevin.show');
    $router->put('/kevin/{id}', 'KevinController@update')->name('kevin.update');
    $router->get('/kevin/{id}/edit', 'KevinController@edit')->name('kevin.edit');
});
