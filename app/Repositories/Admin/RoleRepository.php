<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseRepository;
use App\Models\Admin\NoahRole;
use App\Models\Admin\NoahMasterRoles;
use App\Models\Admin\NoahMaster;

class RoleRepository extends BaseRepository
{

    protected $role;

    function __construct(NoahRole $role)
    {
        $this->role = $role;
    }

    public function getRoleLists($params)
    {
        $where = [];
        if (!empty($params['name'])) {
            $where['name'] = trim($params['name']);
        }
        $orderBy = ['role_id' => 'desc'];
        $data = $this->role->getList('*', $where, $orderBy);

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
    public function checkRoleName($role_name, $roleid = 0)
    {
        $where = $roleid > 0 ? "name = '" . $role_name . "' and roleid<>$roleid" : "name = '" . $role_name . "'";
        return $this->role->whereRaw($where)->select('role_id')->get()->toArray();
    }

    /**
     * 添加
     * @param array $data
     * @return bool
     */
    public function createData($data)
    {
        //unset($data['_token']);
        return $this->role->insert($data);
    }

    /**
     * 修改
     * @param $id 主键id            
     * @param $data 数据数组            
     * @return bool
     */
    public function editData($id, $data)
    {
        return $this->role->where('role_id', $id)->update($data);
    }

    /**
     * 删除操作
     * @param $id 主键id            
     * @return bool
     */
    public function deleteData($id)
    {
        return $this->role->where('role_id', $id)->delete();
    }

    /**
     * 角色下的用户
     * @param type $roleid
     */
    public function getMastersByroleid($roleid)
    {
        $masterids = NoahUserRole::where('role_id',$roleid)->pluck('user_id')->toArray();
        $masters = NoahUser::whereIn('id',$masterids)->orderBy('id','desc')->get()->toArray();

        return $masters;
    }
}
