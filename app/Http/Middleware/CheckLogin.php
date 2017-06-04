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
        $urlStr = $this->route->getActionName();
        $tmpArr = explode('@', $urlStr);
        $this->actionName = strtolower(end($tmpArr));
        $tmpArr = explode('\\', $tmpArr[0]);
        $this->controllerName = strtolower(end($tmpArr));
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
        $this->userInfo = UserRepository::getLoginInfo();
        if (isset($this->userInfo['user_info']['user_name'])) {
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
