<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Route::group(array('prefix' => '', 'namespace' => 'Admin', 'middleware' => ['admin']), function(){
//    Route::get('kevin/test', 'KevinController@test');
//    // 资源路由
//});

Route::group([
    'prefix'        => '',
    'namespace'     => 'Admin',
    'middleware'    => ['web', 'admin'],
], function () {

    Route::get('kevin/test', 'KevinController@test');

});
