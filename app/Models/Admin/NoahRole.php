<?php

namespace App\Models\Admin;

use App\Models\NoahModel;
class NoahRole extends NoahModel
{
    protected $table = 'noah_role';
    protected $primaryKey = 'id';

    static $status = ['1'=>'启用','0'=>'禁用'];

    public function master()
    {
        return $this->belongsToMany('App\Models\NoahMaster', 'noah_master_role', 'user_id', 'role_id');
    }

}
