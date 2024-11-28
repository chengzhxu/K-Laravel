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

use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('index', [ChatController::class, 'index']);
//$router->get('/sfe/user/user_info', 'SalesEffectiveController@findUser');

$router->get('/index', function () use ($router) {
    return [
        'code' => 200,
        'data' => [
            'project' => 'K-Laravel',
            'php'     => PHP_VERSION,
        ]
    ];
});