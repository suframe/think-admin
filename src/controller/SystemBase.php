<?php

namespace suframe\thinkAdmin\controller;

use think\facade\View;

class SystemBase extends Base
{
    protected $urlPre = '/thinkadmin/';

    /**
     * @throws \Exception
     */
    protected function initialize()
    {
        if (!$this->getAdminUser()->isSupper()) {
            throw new \Exception('权限不足');
        }
        parent::initialize();
    }

    protected function setNav($active)
    {
        $navs = [];
        $this->setAdminNavs($navs, $active);
    }

}