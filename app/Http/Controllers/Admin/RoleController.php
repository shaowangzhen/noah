<?php

/**
 * 角色管理
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Admin\NoahRole;
use App\Models\Admin\NoahRoleAction;
use App\Repositories\Admin\RoleRepository;

class RoleController extends BaseController
{

    protected $noahRoleActions;

    public function __construct(NoahRoleAction $noahRoleActions)
    {
        parent::__construct();
        $this->noahRoleActions = $noahRoleActions;
    }

    /**
     * 角色列表
     * @param Request $request
     * @return type
     */
    public function role(Request $request)
    {
        $params = $request->all();
        $lists = (new RoleRepository())->getRoleList($params);
        $status = NoahRole::$status;
        $data = array('lists' => $lists, 'params' => $params,'status'=>$status);

        return view('admin.role', $data);
    }

    /**
     * 角色详情
     * @param int $id
     * @return type
     */
    public function user($id)
    {
        $lists = (new RoleRepository())->getUsersByRoleId($id);
        $status = \App\Models\Admin\NoahUser::$status;
        $data = ['lists'=>$lists,'status'=>$status];
        return view('admin.role_users',$data);
    }

    /**
     * 用户添加
     * @param Request $request
     * @return json
     */
    public function roleAdd(Request $request)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required|min:1|max:100',
                'content' => 'required|min:1|max:200'
            ]);
            $user = $this->getUserInfo();
            $data['name'] = $request->input('name');
            $data['content'] = $request->input('content');
            $data['status'] = $request->input('status');
            $data['creatorid'] = $user['users']['masterid'];
            $isexists = $this->roleRepo->checkRoleName($data['name']);
            if ($isexists) {
                return $this->setCode(self::CODE_ERROR)->setMsg('角色名已存在')->toJson();
            }
            if ($this->roleRepo->createData($data)) {
                // 修改成功
                return $this->setCode(self::CODE_SUCCESS)->setMsg('修改成功')->toJson();
            } else {
                // 修改失败
                return $this->setCode(self::CODE_ERROR)->setMsg('修改失败')->toJson();
            }
        } else {
            return $this->setCode(self::CODE_ERROR)->setMsg('非法请求')->toJson();
        }
    }

    /**
     * 用户信息修改
     * @param Request $request
     * @param int $roleid
     * @return json
     */
    public function roleEdit(Request $request, $roleId)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => 'required|min:1|max:100',
                'content' => 'required|min:1|max:200'
            ]);
            $user = $this->getUserInfo();
            $data['role_name'] = $request->input('name');
            $data['description'] = $request->input('content');
            $data['status'] = $request->input('status');
            $data['creator_id'] = $user['user_info']['id'];
            $roleRepo = new RoleRepository();
            $isexists = $roleRepo->checkRoleName($data['role_name'], $roleId);
            if ($isexists) {
                return $this->setCode(self::CODE_ERROR)->setMsg('角色名已被占用')->toJson();
            }
            if ($roleRepo->editData($roleId, $data)) {
                // 修改成功
                return $this->setCode(self::CODE_SUCCESS)->setMsg('修改成功')->toJson();
            } else {
                // 修改失败
                return $this->setCode(self::CODE_ERROR)->setMsg('修改失败')->toJson();
            }
        } else {
            return $this->setCode(self::CODE_ERROR)->setMsg('非法请求')->toJson();
        }
    }

    /**
     * 角色删除
     * @param int $roleid
     * @return json
     */
    public function roleDel($roleid)
    {
        //超级管理员不允许删除
        $admin = config('auth.admin');
        if ($admin == $roleid) {
            return $this->setCode(self::CODE_ERROR)->setMsg('超级管理员不允许删除')->toJson();
        }
        try {

            $this->noahRoleActions->deleteBy(['role_id'=>$roleid]);
            $status = $this->roleRepo->deleteData($roleid);
            if ($status) {
                return $this->setCode(self::CODE_SUCCESS)->setMsg('删除成功')->toJson();
            } else {
                return $this->setCode(self::CODE_ERROR)->setMsg('删除失败')->toJson();
            }
        } catch (\Exception $exception) {
            return $this->setCode(self::CODE_ERROR)->setMsg('删除失败')->toJson();
        }
    }

}
