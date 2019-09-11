<?php


namespace App\Http\Controllers\OAuth;

use Illuminate\Routing\Controller as BaseController;
use OT\OTest;

class KevinController extends BaseController {

    public function test(){
        return view('kevin/test');
    }
}