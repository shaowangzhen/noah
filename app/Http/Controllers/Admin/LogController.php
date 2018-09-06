<?php

/**
 * 日志管理
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ZebraController;
use Illuminate\Http\Request;
use App\Repositories\LogRepository;

class LogController extends ZebraController
{

    protected $logRepo;

    public function __construct(LogRepository $logRepo)
    {
        parent::__construct();
        $this->logRepo = $logRepo;
    }

    /**
     * 列表
     * @param Request $request
     * @return type
     */
    public function log(Request $request)
    {
        $params = $request->all();
        $lists = $this->logRepo->getLogLists($params);
        
        $data = ['lists' => $lists, 'params' => $params];
        
        return view('admin.log', $data);
    }

    /**
     * 详情
     * @param Request $request
     */
    public function logInfo($logid)
    {
        $data = $this->logRepo->getInfo($logid);
        
        if($data){
            return $this->setCode(self::CODE_SUCCESS)->setMsg('成功')->setData($data)->toJson();
        }else{
            return $this->setCode(self::CODE_ERROR)->setMsg('访问失败')->toJson();
        }
    }
}
