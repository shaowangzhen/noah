<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Repositories\Admin\UserRepository;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    const CODE_SUCCESS = 1;//成功
    const CODE_ERROR = -1;//失败
    const CODE_UPDATE = 2;
    const CODE_NOAUTH = -401;

    protected $result = [
        'code' => self::CODE_ERROR,
        'msg' => '',
        'data' => []
    ];

    public function __construct()
    {
    }

    public function getUserInfo()
    {
        return UserRepository::getLoginInfo();
    }

    public function getUserId()
    {
        $users = UserRepository::getLoginInfo();
        $userId = isset($users['user_info']['id']) ? $users['user_info']['id'] : 0;
        return $userId;
    }

    public function getUserPowerList()
    {
        $users = UserRepository::getLoginInfo();
        return $users['power_list'];
    }

    /**
     * 返回json数据
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->result);
    }

    /**
     * 返回array数据
     * @return array
     */
    public function toArray()
    {
        return $this->result;
    }

    /**
     * 修改code
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->result['code'] = $code;
        return $this;
    }

    /**
     * 修改msg
     * @param $msg
     * @return $this
     */
    public function setMsg($msg)
    {
        $this->result['msg'] = $msg;
        return $this;
    }

    /**
     * 修改data
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->result['data'] = $data;
        return $this;
    }

    /**
     * 通用结果返回
     * @param $result
     * @param $msg
     * @param $optype
     * @param string $url
     * @param int $delay
     * @return mixed
     */
    public function returnDefault($result, $msg, $optype, $url = '', $delay = 3)
    {
        if ($result) {
            return $this->success("恭喜您，操作成功！{$msg}", $optype, $url, $delay);
        } else {
            return $this->failure("操作失败！失败原因:{$msg}", $optype, $url, $delay);
        }
    }

    /**
     * 通用操作成功提示
     * @param string $msg
     * @param string $url
     * @param int $delay
     * @param string $optype
     * @return mixed
     */
    public function success($msg = '恭喜您，操作成功！', $optype = 'redirect', $url = '', $delay = 3)
    {
        if (empty($url)) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        if ($delay == 0) {
            header('location:' . $url);
            return;
        }

        return view('public.success', [
            'msg' => $msg,
            'url' => $url,
            'delay' => $delay,
            'optype' => $optype
        ]);
    }


    /**
     * 通用操作失败提示
     * @param string $msg 提示文字
     * @param string $url 跳转url
     * @param int $delay 跳转延迟（秒）
     * @param string $optype 操作类型
     * @return mixed
     */
    public function failure($msg = '操作失败！', $optype = 'back', $url = '', $delay = 3)
    {
        if (empty($url)) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        if ($delay == 0) {
            header('location:' . $url);
            return;
        }

        return view('public.failure', [
            'msg'    => $msg,
            'url'    => $url,
            'delay'  => $delay,
            'optype' => $optype
        ]);
    }

    /**
     * 向客户端车出json数据
     * @param $code
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function returnJson($code, $msg = '', $data = [])
    {
        $request = app('Illuminate\Http\Request');
        if ($request->ajax()) {
            return response()->json(['code' => $code, 'msg' => $msg, 'data' => $data]);
        } else {
            return response($msg)->header('Content-Type', 'text/html; charset=utf-8');
        }
    }


}
