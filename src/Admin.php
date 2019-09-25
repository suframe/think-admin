<?php

namespace suframe\thinkAdmin;

Class Admin
{

    /**
     * 管理员
     */
    public static function user()
    {
        return static::auth()->user();
    }

    /**
     * auth
     * @return Auth
     */
    public static function auth()
    {
        return \suframe\thinkAdmin\facade\Auth::getInstance();
    }

    /**
     * 应用管理
     * @return facade\Apps
     */
    public static function apps()
    {
        return \suframe\thinkAdmin\facade\Apps::getInstance();
    }

    /**
     * 设置
     * @return facade\Setting
     */
    public static function setting()
    {
        return \suframe\thinkAdmin\facade\Setting::getInstance();
    }

    /**
     * 菜单
     * @return facade\Menu
     */
    public static function menu()
    {
        return \suframe\thinkAdmin\facade\Menu::getInstance();
    }

}