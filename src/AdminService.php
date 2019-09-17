<?php

namespace suframe\thinkAdmin;
use think\Route;
use think\Service;

class AdminService  extends Service
{

    protected $config;
    public function register()
    {
        $this->config = config('thinkAdmin', []);
    }

    public function boot(Route $route)
    {
        $enbale = $this->config['enable'] ?? false;
        if(!$enbale){
            return false;
        }
        \think\facade\Route::get('admin/core/:controller/:action', '\suframe\thinkAdmin\controller\:controller@:action');
    }

}
