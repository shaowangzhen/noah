<?php

namespace App\Models\Admin;

use App\Models\NoahModel;

class NoahAction extends NoahModel
{
    protected $table = 'noah_action';
    protected $primaryKey = 'id';

    public function roles()
    {
        return $this->belongsToMany('App\Models\Admin\NoahRole', 'noah_role_action', 'action_id', 'role_id');
    }
}
