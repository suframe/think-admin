<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminPermissions extends Model
{
    //
    static public $methods = [
        'GET' => 'GET',
        'POST' => 'POST',
        'PUT' => 'PUT',
        'PATCH' => 'PATCH',
        'DELETE' => 'DELETE',
    ];
}
