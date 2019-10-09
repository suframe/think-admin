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
        return "你有一条新的消息：啦啦啦啦";
    }

}