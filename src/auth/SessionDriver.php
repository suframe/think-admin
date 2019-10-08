<?php

namespace suframe\thinkAdmin\auth;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * session
 * Class SessionDriver
 * @package suframe\thinkAdmin\auth
 */
class SessionDriver implements AuthInterface
{
    protected $sessionKey;

    public function __construct()
    {
        $this->sessionKey = config('thinkAdmin.auth.sessionKey', '__think_admin_id__');
    }

    /**
     * 初始化admin
     * @param \think\db\BaseQuery $userDb
     * @return mixed
     */
    public function initAdmin($userDb)
    {
        $id = session($this->sessionKey);
        if (!$id) {
            return false;
        }
        try {
            return $userDb->where('id', $id)->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        // TODO: Implement initAdmin() method.
    }

    /**
     * 登录
     * @param \suframe\thinkAdmin\model\AdminUsers $user
     * @return mixed
     */
    public function login($user)
    {
        session($this->sessionKey, $user->id);
        \think\facade\Session::save();
        return true;
    }

    /**
     * 退出
     * @param \suframe\thinkAdmin\model\AdminUsers $user
     * @return mixed
     */
    public function logout($user)
    {
         session($this->sessionKey, null);
        \think\facade\Session::save();
        return true;
    }
}