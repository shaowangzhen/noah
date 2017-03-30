<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\NoahMaster;
use App\Models\NoahMasterRoles;
use App\Models\NoahRole;
use App\Repositories\UserRepository;
use App\Repositories\MasterRepository;

class MasterController extends BaseController
{

    protected $masterRepo;

    public function __construct(MasterRepository $masterRepo)
    {
        parent::__construct();
        $this->masterRepo = $masterRepo;
    }

    /**
     * 用户列表
     * @param Request $request
     * @return type
     */
    public function master(Request $request)
    {
        $params = $request->all();

        $lists = $this->masterRepo->getMasterList($params);

        $userPowerList = $this->getUserPowerList();

        $data = ['lists' => $lists, 'params' => $params,'powers' => $userPowerList];

        return view('admin.master', $data);
    }

    /**
     * 用户添加
     * @param Request $request
     * @return type
     */
    public function masterAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $res = $this->masterRepo->createData($data);
            if ($res == '添加成功') {
                return $this->setCode(self::CODE_SUCCESS)->setMsg($res)->toJson();
            } else {
                return $this->setCode(self::CODE_ERROR)->setMsg($res)->toJson();
            }
        }
        $data_level = NoahMaster::$dataLevel;
        $status = NoahMaster::$status;
        $roles = $this->masterRepo->getRolesWithMaster();
        $data = [
            'data_level' => $data_level,
            'roles' => $roles,
            'status' => $status
        ];

        return view('admin.master_add', $data);
    }

    /**
     * 用户信息修改
     * @param Request $request
     * @return type
     */
    public function masterEdit(Request $request, $masterid)
    {
        $noahMaster = NoahMaster::where('masterid', $masterid)->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            $res = $this->masterRepo->updateData($data, $masterid);
            if ($res === true) {
                return $this->setCode(self::CODE_SUCCESS)->setMsg('编辑成功')->toJson();
            } else {
                if($res === false) {
                    return $this->setCode(self::CODE_ERROR)->setMsg('编辑失败')->toJson();
                } else {
                    return $this->setCode(self::CODE_ERROR)->setMsg($res)->toJson();
                }
            }
        }
        $data_level = NoahMaster::$dataLevel;
        $status = NoahMaster::$status;
        $roles = $this->masterRepo->getRolesWithMaster($masterid);
        $data = [
            'data_level' => $data_level,
            'master' => $noahMaster,
            'roles' => $roles,
            'status' => $status
        ];

        return view('admin.master_edit', $data);
    }

    public function ajaxDBUser(Request $request) {
        $mastername = $request->input('mastername');
        $data = UserRepository::searchDBUserByName($mastername);
        echo json_encode($data);
    }

    public function masterPwdEdit()
    {
        $masterId = $this->getUserId();
        $masterInfo = $this->masterRepo->getUserInfoByUserId($masterId);
        $data = array('master' => $masterInfo);
        return view('admin.master_pwd_edit', $data);
    }

//    public function masterPwdList()
//    {
//        $masterName = $this->request['mastername'];
//        $mobile = $this->request['mobile'];
//
//        $lists = $this->masterRepo->getThirdMasterList($masterName, $mobile);
//        $paramsList = ['mastername' => $masterName, 'mobile' => $mobile];
//        $data = ['lists' => $lists, 'params' => $paramsList];
////        if($this->isNoah > 0){
////            return $data;
////        }else
//        $this->outputView('admin.master_pwd_list', $data);
//    }
    public function masterPwdModify(Request $request)
    {
        $masterId = $this->getUserId();
        $oldPwd = $request['old'];
        $newPwd = $request['new'];
        $againPwd = $request['again'];
        if($newPwd != $againPwd)
            return $this->setCode(self::CODE_ERROR)->setMsg('两次输入密码不一致！')->toJson();

        $masterInfo = $this->masterRepo->getUserInfoByUserId($masterId);
        $decodePwd = base64_decode($masterInfo['password']);
        //验证密码是否正确
        if(!password_verify(trim($oldPwd), $decodePwd)){
            return $this->setCode(self::CODE_ERROR)->setMsg('旧密码输入错误！')->toJson();
        }
        $savePwd = UserRepository::makePassword(trim($newPwd));
        (new NoahMaster())->updateBy(['password'=> $savePwd], ['masterid'=>$masterId]);
        return $this->setCode(self::CODE_SUCCESS)->setMsg('编辑成功')->toJson();

//        return $this->setCode(self::CODE_SUCCESS)->setMsg('密码修改成功.')->toJson();
    }
}
