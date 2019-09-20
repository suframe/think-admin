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
    public function auth()
    {
        return Auth::create();
    }

}