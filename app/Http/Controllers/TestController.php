<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\ZooCommon;

class TestController extends BaseController
{
    public function test(Request $request)
    {
        return ZooCommon::getInstance()->get('/demo/confs/conf2');
    }
}