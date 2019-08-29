<?php


namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;

class MyPic extends Authenticatable{

    protected $table = 'my_pic';

    protected $fillable = [
        'name','url',
    ];
}