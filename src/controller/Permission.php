<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use think\facade\Cache;
use think\facade\View;

class Permission extends SystemBase
{

    public function index()
    {
        $this->setNav('permission');
        return View::fetch('permission/index');
    }

}