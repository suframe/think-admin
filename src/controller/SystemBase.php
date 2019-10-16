<?php

namespace suframe\thinkAdmin\controller;

use think\facade\View;

class SystemBase extends \suframe\thinkAdmin\controller\Base
{
    protected $urlPre = '/thinkadmin/';

    protected function setNav($active)
    {
        $navs = [
            'system' => ['基本信息', $this->urlA('system/index')],
            'user' => ['用户管理', $this->urlA('user/index')],
            'group' => ['用户组', $this->urlA('group/index')],
            'menu' => ['菜单管理', $this->urlA('menu/index')],
            'permission' => ['权限管理', $this->urlA('permission/index')],
            'logs' => ['系统日志', $this->urlA('logs/index')],
            'setting' => ['系统配置', $this->urlA('setting/index')],
        ];
        $this->setAdminNavs($navs, $active);
    }

}