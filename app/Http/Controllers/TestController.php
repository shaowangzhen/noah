<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Admin\UserRepository;

class TestController extends BaseController
{
    public function test(Request $request)
    {
        echo $request['pwd'] . "<br/>";
        $pwd2 = UserRepository::makePassword($request['pwd']);
        echo $pwd2 . "<br/>";
        if (password_verify(trim($request['pwd']), base64_decode($pwd2))) {
            echo 'success'. "<br/>";
        } else {
            echo 'false' . "<br/>";
        }
    }
}