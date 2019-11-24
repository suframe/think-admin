<?php

namespace suframe\thinkAdmin\controller;

use think\facade\View;

class SystemBase extends Base
{
    protected $urlPre = '/thinkadmin/';

    protected function setNav($active)
    {
        $navs = [
            'system' => ['基本信息', url('/thinkadmin/system/index')->build()],
            'user' => ['用户管理', url('/thinkadmin/user/index')->build()],
            'role' => ['角色管理', url('/thinkadmin/role/index')->build()],
            'menu' => ['菜单管理', url('/thinkadmin/menu/index')->build()],
            'permission' => ['权限管理', url('/thinkadmin/permission/index')->build()],
            'logs' => ['系统日志', url('/thinkadmin/logs/index')->build()],
            'setting' => ['系统配置', url('/thinkadmin/setting/index')->build()],
            'apps' => ['应用管理', url('/thinkadmin/apps/index')->build()],
        ];
        $this->setAdminNavs($navs, $active);
    }

}