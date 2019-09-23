<?php
namespace suframe\thinkAdmin;

Class Admin
{

    /**
     * 管理员
     */
    public function user()
    {
        return static::auth()->user();
    }

    /**
     * @return Auth
     */
    public static function auth()
    {
        return \suframe\thinkAdmin\facade\Auth::create();
    }

}