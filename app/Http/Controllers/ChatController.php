<?php

namespace App\Http\Controllers;

class ChatController
{

    public function index()
    {
        return [
            'id'   => 1,
            'name' => '张三'
        ];
    }
}