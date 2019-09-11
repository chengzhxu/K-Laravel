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

Route::group(array('prefix' => '', 'namespace' => 'OAuth', 'middleware' => []), function(){
    Route::get('kevin/test', 'KevinController@test');
    Route::any('authorize', 'OAuth@authorize');
//    Route::any('token', 'OAuth@token');
//    Route::any('resource', 'OAuth@resource');
//
//
//
//    Route::any('get_access_token', 'OAuth@get_access_token');
//    Route::any('validate_access_token', 'OAuth@validate_access_token');

    // 资源路由
});

