<?php


namespace App\Routes;


class AdminRoute
{
    // 函数名字自定义
    public function route(Registrar $route){
        // 普通路由写法
        $route->get('/test','kevinController@test');
    }
}