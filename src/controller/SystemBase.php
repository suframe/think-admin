<?php

namespace suframe\thinkAdmin\controller;

use think\facade\View;

class SystemBase extends \suframe\thinkAdmin\controller\Base
{
    protected $urlPre = '/thinkadmin/';

    protected function setNav($active)
    {
        $navs = [
            'system' => ['基本信息', $this->urlABuild('system/index')],
            'user' => ['用户管理', $this->urlABuild('user/index')],
            'role' => ['角色管理', $this->urlABuild('role/index')],
            'menu' => ['菜单管理', $this->urlABuild('menu/index')],
            'permission' => ['权限管理', $this->urlABuild('permission/index')],
            'logs' => ['系统日志', $this->urlABuild('logs/index')],
            'setting' => ['系统配置', $this->urlABuild('setting/index')],
        ];
        $this->setAdminNavs($navs, $active);
    }

}