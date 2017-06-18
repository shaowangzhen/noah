<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ZebraController;
use App\Repositories\Admin\UserRepository;
use App\Repositories\WhitelistRepository;
use Illuminate\Http\Request;
use DB;

class WhitelistController extends ZebraController
{
    protected $whiteRepo;
    protected $userRepo;
    protected $request;

    public function __construct(Request $request,WhitelistRepository $whiteRepo,UserRepository $userRepo)
    {
        parent::__construct();
        $this->request = $request;
        $this->whiteRepo = $whiteRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * 白名单列表
     */
    public function whiteList()
    {
        $params = $this->request->all();
        $lists = $this->whiteRepo->getWhiteList($params);
        $data = ['lists' => $lists, 'params' => $params];
        return view('admin.whitelist', $data);
    }
    /**
     * 白名单删除
     */
    public function whiteDel()
    {
        $id = $_POST['id'];
        $this->whiteRepo->delData($id);
    }

    /**
     * 白名单添加
     */
    public function whiteAdd(){
        $master = $this->userRepo->getLoginInfo();
        $masterId = $master['users']['masterid'];
        $data = ['masterid'=>$masterId];
        return view('admin.whitelist_add',$data);

    }
    public function addCheck(Request $request){
        $data = $request->all();
        $res = $this->whiteRepo->addData($data);

        if($res == true){
            header("location:/admin/whitelist");
        } else{
            echo "<script>alert('格式有误，请重新输入！');</script>";
            return($this->whiteAdd());
        }
    }
}