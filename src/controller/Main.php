<?php

namespace suframe\thinkAdmin\controller;


use think\facade\View;

class Main extends Base
{

    public function index()
    {
        return View::fetch('main/index');
    }

}