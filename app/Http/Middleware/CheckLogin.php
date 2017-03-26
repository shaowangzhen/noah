<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Route;
use Closure;
use App\Repositories\UserRepository;

class CheckLogin
{

    protected $route;
    protected $userInfo;
    public $controllerName;
    public $actionName;

    public function __construct(Route $route)
    {
        $this->route = $route;
        // 获取当前的controller和action
        $urlStr = $this->route->getActionName();
        $_tmpArr = explode('@', $urlStr);
        $this->actionName = strtolower(end($_tmpArr));
        $_tmpArr = explode('\\', $_tmpArr[0]);
        $this->controllerName = strtolower(end($_tmpArr));
    }

    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 验证登录
        $this->userInfo = UserRepository::getLoginInfo();
        if (isset($this->userInfo['users']['mastername'])) {
            if (($this->controllerName == 'logincontroller') && ($this->actionName == 'index')) {
                return redirect('/');
            }
        } else {
            if ($this->controllerName != 'logincontroller') {
                return redirect('/login');
            }
        }
        // 权限验证
        $checkResult = UserRepository::checkPower($this->controllerName, $this->actionName, $this->userInfo['powers']);
        if (!$checkResult) {
            $msg = "您没有" . $this->controllerName . " " . $this->actionName . "权限";
            if ($request->ajax()) {
                $data = ['msg' => $msg,'code' => -401];
                return response()->json($data);
            } else {
                return view('errors.error', ['msg' => $msg,'optype' => 'parent_reload','url' => url()]);
            }
        }
        return $next($request);
    }

}
