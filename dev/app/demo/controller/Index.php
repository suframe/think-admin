<?php
declare (strict_types = 1);

namespace app\demo\controller;

class Index
{
    /**
     * @menu 菜单
     * @return string
     */
    public function index()
    {
        return '您好！这是一个[demo]示例应用';
    }
}
