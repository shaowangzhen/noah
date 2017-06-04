<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Admin\UserRepository;

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
        $userName = $request->input('username');
        $pwd = $request->input('password');
        if (!$userName || !$pwd) {
            return $this->setCode(self::CODE_ERROR)->setMsg('用户名或密码不能为空!')->toJson();
        }

        $res = UserRepository::checkLogin($userName, $pwd);

        if ($res['code'] == 0) {
            return $this->setCode(self::CODE_ERROR)->setMsg('该用户已被禁用')->toJson();
        } elseif ($res['code'] == 1) {
            return $this->setCode(self::CODE_SUCCESS)->setMsg('登录成功!')->toJson();
        } elseif($res['code'] == 2) {
            return $this->setCode(self::CODE_ERROR)->setMsg('抱歉，您没有系统权限')->toJson();
        } elseif ($res['code'] == -1) {
            return $this->setCode(self::CODE_ERROR)->setMsg('该用户不存在！')->toJson();
        } elseif ($res['code'] == -2) {
            return $this->setCode(self::CODE_ERROR)->setMsg('用户名或密码错误！')->toJson();
        }
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
