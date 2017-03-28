<?php

/**
 * 角色管理
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ZebraController;
use Illuminate\Http\Request;
use App\Repositories\RoleActionRepository;
use App\Repositories\ActionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\CityRepository;
use App\Repositories\UserRepository;
use DB;

class RoleActionController extends ZebraController
{

    protected $roleActionRepo;
    protected $roleRepo;

    public function __construct(RoleActionRepository $roleActionRepo, RoleRepository $roleRepo)
    {
        parent::__construct();
        $this->roleActionRepo = $roleActionRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * 角色权限
     * @param $id
     * @return type
     */
    public function set($id)
    {
        //操作权限
        $actionRepo = new ActionRepository();
        // 查看已经有的权限
        $roleActionsList = $this->roleActionRepo->getActionsList($id);
        // 查看当前用户可支配权限
        $actions = $this->roleActionRepo->getActionsLists();
        // 获得所有权限
        $getList = $actionRepo->getRoleAction($actions, $roleActionsList);
        $json = json_encode($getList);

        //数据权限
        //$citys = CityRepository::getAreaCitys();      //按区域
        $citys = CityRepository::getProCitys();         //按省
        $resroles = UserRepository::listResRoleByRoleids($id, 'resid', 'city');
        $cityList = $this->roleActionRepo->getRoleCity($citys, $resroles);

        $data = ['json' => $json, 'role_id' => $id, 'citys' => $cityList];

        return view('admin.roleaction', $data);
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
                die();
            } else {
                return $this->setCode(self::CODE_ERROR)->setMsg('失败操作')->toJson();
                die();
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
                        die();
                    }
                }
            }
            // 增加新的数据
            if (!empty($info['add'])) {
                foreach ($info['add'] as $k => $v) {
                    $data['roleid'] = $role_id;
                    $data['actionid'] = $v;
                    $data['creatorid'] = $this->userId;
                    if (!$this->roleActionRepo->createData($data)) {
                        // 回滚
                        DB::rollback();
                        return $this->setCode(self::CODE_ERROR)->setMsg('失败操作')->toJson();
                        die();
                    }
                }
            }
            // 提交
            DB::commit();
            return $this->setCode(self::CODE_SUCCESS)->setMsg('删除成功')->toJson();
            die();
        } else {
            return $this->setCode(self::CODE_ERROR)->setMsg('角色权限未发生改变')->toJson();
        }
    }

}
