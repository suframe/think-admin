<?php
declare (strict_types = 1);

namespace app\demo\controller;

use think\facade\Db;

class Index
{
    /**
     * @menu 菜单
     * @return string
     */
    public function index()
    {
        $user = Db::table('admin_users');
        print_r($user->select());
        return '您好！这是一个[demo]示例应用';
    }
}
