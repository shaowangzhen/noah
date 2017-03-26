<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;

class LoginController extends BaseController
{
    public function index()
    {
        $userInfo = UserRepository::getLoginInfo();
        if(!empty($userInfo)){
            return redirect('/');
        }
        return view('login');
    }

    public function check(Request $request)
    {
        $username = $request->input('username');
        $pwd = $request->input('password');
        if (!$username || !$pwd) {
            return $this->setCode(self::CODE_ERROR)->setMsg('用户名或密码不能为空!')->toJson();
        }
        // 登录验证
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $request->getClientIp();
        $res = UserRepository::checkLogin($username, $pwd, '', false); //暂不开启短信验证
        if ($res['code'] == 1) {
            return $this->setCode(self::CODE_SUCCESS)->setMsg('登录成功!')->toJson();
        } elseif(isset($res['code'])) {
            return $this->setCode($res['code'])->setMsg($res['msg'])->setData($ip)->toJson();
        }
        return $this->setCode(self::CODE_ERROR)->setMsg('用户名或密码错误！')->toJson();
    }

    public function logout()
    {
        try {
            UserRepository::delLoginInfo();
            return redirect('/login');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}
