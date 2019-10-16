<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use think\facade\Cache;
use think\facade\View;

class Group extends SystemBase
{

    public function index()
    {
        $this->setNav('group');
        return View::fetch('group/index');
    }

}