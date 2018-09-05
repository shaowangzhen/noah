<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\Common;

class TestController extends BaseController
{
    public function test(Request $request)
    {
        return 'success';
    }
}