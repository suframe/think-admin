<?php

namespace suframe\thinkAdmin;

use think\Route;
use think\Service;

class AdminService extends Service
{

    protected $config;
    protected $enable = false;

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    public function register()
    {
        $this->config = config('thinkAdmin', []);
        $this->enable = $this->config['enable'] ?? false;
        if (!$this->enable) {
            return false;
        }

        $this->registerRouteMiddleware();
    }

    /**
     * @param Route $route
     * @return bool|void
     */
    public function boot(Route $route)
    {
        if (!$this->enable) {
            return false;
        }
        $route->get('admin/core/:controller/:action', '\suframe\thinkAdmin\controller\:controller@:action')
            ->middleware($this->routeMiddleware, 'admin');
    }

    /**
     * 路由中间件
     */
    protected function registerRouteMiddleware()
    {
        $this->routeMiddleware = config('thinkAdmin.routeMiddleware', []);
    }
}
