<?php

namespace App\Repositories\Admin;

use App\Repositories\BaseRepository;
use App\Models\Admin\NoahRoleActions;
use App\Models\Admin\NoahAction;
use App\Models\Admin\NoahResRole;
use DB;

class RoleActionRepository extends BaseRepository
{

    protected $roleActions;

    public function __construct(NoahRoleActions $roleActions)
    {
        $this->roleActions = $roleActions;
    }

    /**
     * 通过角色id 获取当前已经有的权限
     * @param $roleId 角色id
     */
    public function getActionsList($roleId)
    {
        return $this->roleActions->where(['roleid' => $roleId])->get();
    }

    /**
     * 所有权限
     * 为启用的权限
     */
    public function getActionsLists()
    {
        return NoahAction::select('actionid')->where('status',1)->get()->toarray();
    }

    /**
     * 添加数据
     */
    public function createData($data)
    {
        unset($data['_token']);
        return $this->roleActions->insert($data);
    }

    /**
     * 删除操作
     * @param $roleid 角色id
     * @param $actionid 权限id
     * @return bool
     */
    public function deleteData($roleid, $actionid)
    {
        return $this->roleActions->where('roleid', $roleid)->where('actionid', $actionid)->delete();
    }

    /**
     * 通过角色删除
     * @param $roleid 角色id
     * @return bool 
     */
    public function deleteRoleData($roleid)
    {
        return $this->roleActions->where('roleid', $roleid)->delete();
    }

    /**
     * 数据权限编辑
     * @param type $data
     * @return bool
     */
    public function editRoleCitys($data)
    {
        $roleid = $data['role_id'];
        if(!$roleid){
            return false;
        }
        
        $citys = isset($data['citys'])?$data['citys']:[];
        $exist_citys = UserRepository::listResRoleByRoleids($roleid, 'resid', 'city');
        $double_citys = array_intersect($exist_citys, $citys);
        $add_citys = array_diff($citys,$double_citys);
        $del_citys = array_diff($exist_citys,$double_citys);
        
        DB::beginTransaction();
        $flag = true;
        if($add_citys){
            $insert_data = [];
            foreach($add_citys as $cityid){
                $insert_data[] = ['restype'=>'city','resid'=>$cityid,'roleid'=>$roleid];
            }
            $flag = OpResRole::insert($insert_data);
        }
        if($del_citys){
            $flag && $flag = OpResRole::whereIn('resid',$del_citys)->where(['roleid'=>$roleid,'restype'=>'city'])->delete();
        }
        if(!$flag){
            DB::rollback();
            return false;
        }
        DB::commit();
        
        return true;
    }
    
    /**
     * 数据权限对照
     * @param type $allCitys
     * @param type $roleCitys
     */
    public function getRoleCity($allCitys,$roleCitys)
    {
        foreach($allCitys as $key=>$proCitys){
            foreach($proCitys as $k=>$city){
                in_array($city['cityid'],$roleCitys) && $allCitys[$key][$k]['checked'] = true;
            }
            if(empty($proCitys)){
                unset($allCitys[$key]);
            }
        }
        return $allCitys;
        
    }
}
