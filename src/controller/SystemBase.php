<?php

namespace suframe\thinkAdmin\controller;

use think\facade\View;

class SystemBase extends Base
{
    protected $urlPre = '/thinkadmin/';

    protected function setNav($active)
    {
        $navs = [];
        $this->setAdminNavs($navs, $active);
    }

}