<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;

/**
 * 认证接口
 * Class Auth
 * @package suframe\thinkAdmin\controller
 */
class Auth extends Base
{

    /**
     * 退出登录
     * @return bool
     */
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