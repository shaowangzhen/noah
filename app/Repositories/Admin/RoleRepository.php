<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseRepository;
use App\Models\Admin\NoahRole;
use App\Models\Admin\NoahUserRole;
use App\Models\Admin\NoahUser;

class RoleRepository extends BaseRepository
{

    public function getRoleList($params)
    {
        $where = [];
        if (!empty($params['name'])) {
            $where['name'] = trim($params['name']);
        }
        $orderBy = ['id' => 'desc'];
        $data = (new NoahRole())->getList(['id','role_name','description','status','created_at'], $where, $orderBy);

        return $data;
    }

    /**
     * 获取当前id条件数据表信息
     * 
     * @param $id 主键id            
     */
    static public function getInfo($id)
    {
        return OpRole::where([
                    'role_id' => $id
                ])->first();
    }

    /**
     * 检测name是否重复
     * @param type $name
     */
    public function checkRoleName($roleName, $roleId = 0)
    {
        $where = $roleId > 0 ? "role_name = '" . $roleName . "' and id<>$roleId" : "role_name = '" . $roleName . "'";
        return (new NoahRole())->whereRaw($where)->select('id')->get()->toArray();
    }

    /**
     * 添加
     * @param array $data
     * @return bool
     */
    public function createData($data)
    {
        //unset($data['_token']);
        return (new NoahRole())->insert($data);
    }

    /**
     * 修改
     * @param $id 主键id            
     * @param $data 数据数组            
     * @return bool
     */
    public function editData($id, $data)
    {
        return (new NoahRole())->where('id', $id)->update($data);
    }

    /**
     * 删除操作
     * @param $id 主键id            
     * @return bool
     */
    public function deleteData($id)
    {
        return (new NoahRole())->where('role_id', $id)->delete();
    }

    /**
     * 角色下的用户
     */
    public function getUsersByRoleId($roleid)
    {
        $userIds = NoahUserRole::where('role_id',$roleid)->pluck('user_id')->toArray();
        $users = NoahUser::whereIn('id',$userIds)->orderBy('id','desc')->get()->toArray();

        return $users;
    }
}
