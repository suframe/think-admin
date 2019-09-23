<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;

class Auth extends Base
{

    public function logout()
    {
        return Admin::auth()->logout();
    }


    /**
     * 登录
     * @return string
     * @throws \Exception
     */
    public function login()
    {
        $username = $this->requirePost('username');
        $password = $this->requirePost('password');
        return Admin::auth()->login($username, $password);
    }

}