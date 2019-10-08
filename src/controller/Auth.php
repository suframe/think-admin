<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use think\facade\View;

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
        Admin::auth()->logout();
        return redirect('/thinkadmin/auth/login');
    }

    /**
     * 登录
     * @return string
     * @throws \Exception
     */
    public function login()
    {
        if ($this->request->isPost()) {
            $username = $this->requirePost('username');
            $password = $this->requirePost('password');
            $rs = Admin::auth()->login($username, $password);
            if($rs){
                return redirect('/thinkadmin/main/index');
            }
            return $rs ? '登录成功' : '登录失败';
        }
        return View::fetch('auth/login');
    }

}