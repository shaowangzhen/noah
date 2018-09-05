<?php

/**
 * 角色管理
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Repositories\Admin\RoleActionRepository;
use App\Repositories\Admin\ActionRepository;
use App\Repositories\Admin\RoleRepository;
use App\Repositories\Admin\UserRepository;
use DB;

class RoleActionController extends BaseController
{

    protected $roleRepo;

    public function __construct(RoleActionRepository $roleActionRepo, RoleRepository $roleRepo)
    {
        parent::__construct();
        $this->roleRepo = $roleRepo;
    }

    /**
     * 角色权限
     * @param $id
     * @return type
     */
    public function set($id)
    {
        // 查看已经有的权限
        $roleActionRepo = new RoleActionRepository();
        $roleActionsList = $roleActionRepo->getActionsList($id);
        // 查看当前用户可支配权限
        $actions = $roleActionRepo->getActionsLists();
        // 获得所有权限
        $actionRepo = new ActionRepository();
        $getList = $actionRepo->getRoleAction($actions, $roleActionsList);
        $json = json_encode($getList);

        $data = ['json' => $json, 'role_id' => $id];

        return view('admin.role_action', $data);
    }

    /**
     * 权限分配
     * @param Request $request
     * @return type
     */
    public function edit(Request $request)
    {
        $info = $request->all();
        if(isset($info['city'])){
            $res = $this->roleActionRepo->editRoleCitys($info);
            if($res){
                return $this->setCode(self::CODE_SUCCESS)->setMsg('操作成功')->toJson();
            } else {
                return $this->setCode(self::CODE_ERROR)->setMsg('失败操作')->toJson();
            }
        }
        $role_id = $info['role_id'];
        if ((!empty($role_id)) && (!empty($info['add']) || !empty($info['del']))) {
            // 开启事物
            DB::beginTransaction();
            // 删除更改的数据
            if (!empty($info['del'])) {
                foreach ($info['del'] as $dk => $dv) {
                    if (!$this->roleActionRepo->deleteData($role_id, $dv)) {
                        // 回滚
                        DB::rollback();
                        return $this->setCode(self::CODE_ERROR)->setMsg('失败操作')->toJson();
                    }
                }
            }
            // 增加新的数据
            if (!empty($info['add'])) {
                foreach ($info['add'] as $k => $v) {
                    $data['role_id'] = $role_id;
                    $data['action_id'] = $v;
                    $data['creatorid'] = $this->getUserId();
                    if (!$this->roleActionRepo->createData($data)) {
                        // 回滚
                        DB::rollback();
                        return $this->setCode(self::CODE_ERROR)->setMsg('失败操作')->toJson();
                    }
                }
            }
            // 提交
            DB::commit();
            return $this->setCode(self::CODE_SUCCESS)->setMsg('删除成功')->toJson();
        } else {
            return $this->setCode(self::CODE_ERROR)->setMsg('角色权限未发生改变')->toJson();
        }
    }

}
