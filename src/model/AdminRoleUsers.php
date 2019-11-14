<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminRoleUsers extends Model
{
    //

    public static function getRoleByUser($user_id)
    {
        return AdminRoleUsers::where('user_id', $user_id)
            ->field('role_id')
            ->column('role_id');
    }

}
