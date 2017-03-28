<?php

namespace App\Models;


class NoahAction extends NoahModel
{
    protected $table = 'noah_action';
    protected $primaryKey = 'actionid';
    public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany('App\Models\NoahRole', 'noah_role_actions', 'actionid', 'roleid');
    }
}
