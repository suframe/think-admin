<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use think\facade\Route as RouteAlias;
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
        $hasCaptcha = $this->hasCaptcha();
        if ($this->request->isPost()) {
            try {
                $captcha = $this->requirePost('captcha');
                if (!captcha_check($captcha)) {
                    //验证失败
                    throw new \Exception('验证码错误');
                }
                $username = $this->requirePost('username', '请输入用户名');
                $password = $this->requirePost('password', '请输入密码');
                $rs = Admin::auth()->login($username, $password);
                if ($rs) {
                    $parent_url = $this->request->param('parent_url');
                    return redirect($parent_url ?: '/thinkadmin/main/index');
                }
                Session::set('login_message', '用户名或密码错误');
            } catch (\Exception $e) {
                Session::set('login_message', $e->getMessage());
                return redirect('/thinkadmin/main/index');
            }
        }
        View::assign('message', Session::pull('login_message'));
        if ($hasCaptcha) {
            RouteAlias::get('captcha/[:id]', "\\think\\captcha\\CaptchaController@index");
        }
        View::assign('hasCaptcha', $hasCaptcha);
        return View::fetch('auth/login');
    }

    protected function hasCaptcha()
    {
        return config('thinkAdmin.auth.captcha') && class_exists("\\think\\captcha\\CaptchaController");
    }

    public function captcha($id = '')
    {
        return captcha($id);
    }

}