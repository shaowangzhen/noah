<?php

namespace App\Repositories\Admin;

use App\Models\Admin\NoahRoleAction;
use App\Repositories\BaseRepository;
use App\Models\Admin\NoahAction;

class RoleActionRepository extends BaseRepository
{
    /**
     * 通过角色id 获取当前已经有的权限
     * @param $roleId 角色id
     */
    public function getActionsList($roleId)
    {
        return (new NoahRoleAction())->where(['role_id' => $roleId])->get();
    }

    /**
     * 所有权限
     * 为启用的权限
     */
    public function getActionsLists()
    {
        return NoahAction::select('id')->where('status',1)->get()->toarray();
    }

    /**
     * 添加数据
     */
    public function createData($data)
    {
        unset($data['_token']);
        return (new NoahRoleAction())->insert($data);
    }

    /**
     * 删除操作
     * @param $roleid 角色id
     * @param $actionId 权限id
     * @return bool
     */
    public function deleteData($roleid, $actionId)
    {
        return (new NoahRoleAction())->where('role_id', $roleid)->where('id', $actionId)->delete();
    }

    /**
     * 通过角色删除
     * @param $roleId 角色id
     * @return bool 
     */
    public function deleteRoleData($roleId)
    {
        return (new NoahRoleAction())->where('role_id', $roleId)->delete();
    }
}
