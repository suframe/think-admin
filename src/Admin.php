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
        return \suframe\thinkAdmin\facade\Auth::create();
    }


}