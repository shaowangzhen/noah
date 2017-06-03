<?php

namespace App\Models\Admin;

use App\Models\NoahModel;
class NoahRole extends NoahModel
{
    protected $table = 'noah_role';
    protected $primaryKey = 'roleid';
    public $timestamps = false;

    static $status = ['1'=>'启用','-1'=>'禁用'];

    public function master()
    {
        return $this->belongsToMany('App\Models\NoahMaster', 'noah_master_roles', 'masterid', 'roleid');
    }

}
