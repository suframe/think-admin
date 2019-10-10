<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use think\exception\ValidateException;
use think\facade\Session;
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
            try {
                $username = $this->requirePost('username', '请输入用户名');
                $password = $this->requirePost('password','请输入密码');
                $rs = Admin::auth()->login($username, $password);
                if ($rs) {
                    return redirect('/thinkadmin/main/index');
                }
                return $rs ? '登录成功' : '登录失败';
            } catch (\Exception $e) {
                Session::set('login_message', $e->getMessage());
                return redirect('/thinkadmin/main/index');
            }
        }
        View::assign('message', Session::pull('login_message'));
        return View::fetch('auth/login');
    }

}