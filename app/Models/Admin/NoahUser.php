<?php

namespace App\Models\Admin;

use App\Models\NoahModel;

class NoahUser extends NoahModel
{
    protected $table = 'noah_user';
    protected $primaryKey = 'id';
    static $status = ['-1' => '删除', '0' => '禁用', '1' => '启用'];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\NoahRole', 'noah_user_role', 'user_id', 'role_id');
    }
}
