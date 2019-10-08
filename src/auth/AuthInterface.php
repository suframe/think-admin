<?php
/**
 * +----------------------------------------------------------------------
 * | 九正科技实业有限公司
 * +----------------------------------------------------------------------
 * | Copyright (c) 2017 http://www.jc001.cn All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 钱进 <330576744@qq.com>  2019/10/8 14:00
 * +----------------------------------------------------------------------
 */

namespace suframe\thinkAdmin\auth;

interface AuthInterface
{

    /**
     * 初始化admin
     * @param \think\db\BaseQuery $userDb
     * @return mixed
     */
    public function initAdmin($userDb);

    /**
     * 登录
     * @param \suframe\thinkAdmin\model\AdminUsers $user
     * @return mixed
     */
    public function login($user);

    /**
     * 退出
     * @param \suframe\thinkAdmin\model\AdminUsers $user
     * @return mixed
     */
    public function logout($user);

}