<?php

namespace suframe\thinkAdmin\controller;


use think\facade\View;

class Main extends Base
{

    public function index()
    {

        View::assign('admin', $this->getAdminUser());
        return View::fetch('main/index');
    }

    public function message()
    {
        return View::fetch('main/message');
    }
}