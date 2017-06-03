<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Route;
use Closure;
use App\Repositories\Admin\UserRepository;

class CheckLogin
{

    protected $route;
    protected $userInfo;
    public $controllerName;
    public $actionName;

    public function __construct(Route $route)
    {
        $this->route = $route;
        dd($this->route);
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
        return $next($request);
    }

}
