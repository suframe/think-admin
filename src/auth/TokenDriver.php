<?php

namespace suframe\thinkAdmin\auth;

use suframe\thinkAdmin\model\AdminUsers;

class TokenDriver implements AuthInterface
{

    /**
     * 初始化admin
     * @param \think\db\BaseQuery $userDb
     * @return mixed
     */
    public function initAdmin($userDb)
    {
        $token = app()->request->param(config('thinkAdmin.tokenName', 'token'));
        if (!$token) {
            return false;
        }
        $rs = $userDb->where('access_token', $token)->find();
        if ($rs) {
            return $rs;
        }
    }


    /**
     * 登录
     * @param AdminUsers $user
     * @return mixed
     */
    public function login($user)
    {
        $token = $this->genToken();
        $user->access_token = $token;
        $user->login_fail = 0;
        $user->save();
        return $token;
    }

    /**
     * 退出
     * @param AdminUsers $user
     * @return mixed
     */
    public function logout($user)
    {
        $user->access_token = null;
        return $user->save();
    }

    /**
     * 生成token
     * @return string
     */
    protected function genToken()
    {
        $salt = config('thinkAdmin.tokenSalt', '__think_admin_salt__');
        return md5(session_create_id() . $salt);
    }

}