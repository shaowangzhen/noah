<?php

namespace App\Models\Admin;

use App\Models\NoahModel;

class NoahMaster extends NoahModel
{
    protected $table = 'noah_master';
    protected $primaryKey = 'masterid';
    public $timestamps = false;
    static $status = ['-1' => '删除', '0' => '禁用', '1' => '启用'];
    static $dataLevel = ['1' => '城市权限', '2' => '区域权限', '3' => '公司权限', '4' => '员工权限'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\NoahRole', 'noah_master_roles', 'masterid', 'roleid');
    }
}
