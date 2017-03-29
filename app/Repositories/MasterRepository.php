<?php

namespace App\Repositories;

use DB;
use App\Models\NoahMaster;
use App\Models\NoahMasterRoles;

class MasterRepository extends BaseRepository
{

    protected $master;

    public function __construct(NoahMaster $master)
    {
        $this->master = $master;
    }

    /**
     * 根据fullname获取单条数据
     * @param type $fullname
     * @return type
     */
    public function getIdByFullName($fullname)
    {
        $master = $this->master->getOne(['masterid'], ['fullname'=>$fullname]);
        $masterId = isset($master['masterid'])?$master['masterid']:0;
        return $masterId;
    }
    
    /**
     * 用户列表
     * @param array $params
     * @return array
     * @author changke
     */
    public function getMasterList($params)
    {
        $where['in']['status'] = [0,1];
        if (!empty($params['mastername'])) {
            $where['mastername'] = trim($params['mastername']);
        }
        if (!empty($params['mobile'])) {
            $where['mobile'] = trim($params['mobile']);
        }
        $orderBy = ['masterid' => 'desc'];
        $lists = $this->master->getList('*', $where, $orderBy);
        $status = NoahMaster::$status;
        foreach($lists as $k=>$list){
            $lists[$k]['statusname'] = $status[$list['status']];
        }
        return $lists;
    }
    
    /**
     * 获取角色分配列表
     * @param type $masterid
     */
    public function getRolesWithMaster($masterid = 0)
    {
        $roles = $masterid?UserRepository::getRoleList($masterid):[];
        $allRoles = UserRepository::getAllRoleList();
        $data = [];
        foreach($allRoles as $roleid=>$rolename){
            $checked = array_key_exists($roleid,$roles)?true:false;
            $data[] = ['roleid'=>$roleid,'rolename'=>$rolename,'checked'=>$checked];
        }
        return $data;
    }
    
    /**
     * 添加数据
     * @param type $data
     * @param type $masterid
     * @return boolean
     */
    public function createData($data)
    {
        $user = $this->getUserInfo();
        $userId = $user['users']['masterid'];
        $noahMaster = new NoahMaster;
        //查找是否已经存在
        $useOld = $noahMaster->getOne('masterid',['in'=>['status'=>[0,1]],'mastername'=>$data['mastername']]);
        if(!empty($useOld)){
            return '该用户名已经添加,不能重复添加';
        }

        $noahMaster->fullname = isset($data['fullname'])?$data['fullname']:'';
        $noahMaster->mobile = isset($data['mobile'])?$data['mobile']:'';
        $noahMaster->email = isset($data['email'])?$data['email']:'';
        $noahMaster->deptname = isset($data['deptname'])?$data['deptname']:'';
        $noahMaster->mastername = isset($data['mastername'])?$data['mastername']:'';
        if(isset($data['password'])) {
            $noahMaster->password = UserRepository::makePassword(trim($data['password']));
        } else {
            $noahMaster->password = UserRepository::makePassword(trim($data['mastername']));
        }
        $noahMaster->creatorid = $userId;
        $noahMaster->status = $data['status'];

        DB::beginTransaction();
        $flag = $noahMaster->save();
        $masterid = $noahMaster->masterid;
        //角色添加
        $roleIds = !empty($data['roleids']) ? $data['roleids'] : [];
        if($flag && $roleIds){
            $insert_data = [];
            foreach($roleIds as $id){
                $insert_data[] = ['masterid'=>$masterid,'roleid'=>$id,'creatorid'=>$userId];
            }
            $flag = NoahMasterRoles::insert($insert_data);
        }
        if(!$flag){
            DB::rollback();
            return '添加失败';
        }
        DB::commit();
        return '添加成功';
    }
    /**
     * 更新数据
     * @param type $data
     * @param type $masterid
     * @return boolean
     */
    public function updateData($data,$masterid)
    {
        $user = $this->getUserInfo();
        $userId = $user['users']['masterid'];
        $noahMaster = NoahMaster::find($masterid);
        $noahMaster->fullname = $data['fullname'];
        $noahMaster->mobile = $data['mobile'];
        $noahMaster->email = $data['email'];
        $noahMaster->deptname = $data['deptname'];
        if(isset($data['password']) && trim($data['password']) != '') {
            $noahMaster->password = UserRepository::makePassword(trim($data['password']));
        }
        $noahMaster->creatorid = $userId;
        $noahMaster->status = $data['status'];

        DB::beginTransaction();
        $flag = $noahMaster->save();
        //角色修改
        $roles = UserRepository::getRoleList($masterid);
        $hasIds = $roles?array_flip($roles):[];
        $roleIds = isset($data['roleids'])?$data['roleids']:[];
        $doubleIds = array_intersect($hasIds, $roleIds);
        $addIds = array_diff($roleIds, $doubleIds);
        $delIds = array_diff($hasIds, $doubleIds);
        if($flag && $addIds){
            $insert_data = [];
            foreach($addIds as $id){
                $insert_data[] = ['masterid'=>$masterid,'roleid'=>$id,'creatorid'=>$userId];
            }
            $flag = NoahMasterRoles::insert($insert_data);
        }
        if($flag && $delIds){
            $flag = NoahMasterRoles::whereIn('roleid',$delIds)->where('masterid',$masterid)->delete();
        }
        if(!$flag){
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

    /**
     * master数据导入
     */
    public function cpMaster()
    {
        $rbacMaster = DB::table('rbac_master')->where('masterid','>','0')->get();
        $sql = 'insert `erp_master` (`masterid`,`user_pic`,`cityid`,`mastername`,`fullname`,`mobile`,`email`,`deptname`,`creatorid`,`status`) values';
        $data = '';
        foreach ($rbacMaster as $m) {
            $user_pic = $m->user_pic?$m->user_pic:'';
            $data .= "('{$m->masterid}','{$user_pic}','{$m->cityid}','{$m->mastername}','{$m->fullname}','{$m->mobile}','{$m->email}','{$m->deptname}','{$m->creatorid}','{$m->status}'),";
        }
        $sql .= trim($data,',');
        if(!isset($_GET['insert'])){
            echo $sql;die;
        }else{
            DB::insert($sql);
        }
    }

    public function getAllMaster()
    {
        return $this->master->getAll('*');
    }

}
