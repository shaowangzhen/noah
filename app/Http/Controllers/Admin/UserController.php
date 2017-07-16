<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\NoahUser;
use App\Models\Admin\NoahUserRole;
use App\Models\Admin\NoahRole;
use App\Repositories\Admin\UserRepository;
use App\Repositories\Admin\MasterRepository;

class UserController extends BaseController
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
    public function user(Request $request)
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
    public function userAdd(Request $request)
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
        $data_level = NoahUser::$dataLevel;
        $status = NoahUser::$status;
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
    public function userEdit(Request $request, $masterid)
    {
        $noahMaster = NoahUser::where('id', $masterid)->first();
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
        $data_level = NoahUser::$dataLevel;
        $status = NoahUser::$status;
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

    public function userPwdEdit()
    {
        $masterId = $this->getUserId();
        $masterInfo = $this->masterRepo->getUserInfoByUserId($masterId);
        $data = array('master' => $masterInfo);
        return view('admin.master_pwd_edit', $data);
    }

    public function userPwdModify(Request $request)
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
        (new NoahUser())->updateBy(['password'=> $savePwd], ['id'=>$masterId]);
        return $this->setCode(self::CODE_SUCCESS)->setMsg('编辑成功')->toJson();
    }
}
