<?php

namespace suframe\thinkAdmin;

use DirectoryIterator;
use think\Route;
use think\Service;

class AdminService extends Service
{

    protected $enable = false;
    protected $routeMiddleware = [];

    public function register()
    {
        $this->enable = config('thinkAdmin.enable', false);
        if (!$this->enable) {
            return false;
        }
        $this->initAdmin();
        $this->createMigrations();
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
        $middleware = config('thinkAdmin.routeMiddleware');
        $route->post('thinkadmin/auth/login', '\suframe\thinkAdmin\controller\auth@login')->token();
        if (config('app.auto_multi_app') === true) {
            //多应用，通过应用目录下 middleware.php文件自己设置
            if (strpos($this->app->request->pathinfo(), config('app.uri_pre', 'thinkadmin/')) === 0) {
                $route->any('thinkadmin/:controller/:action', '\suframe\thinkAdmin\controller\:controller@:action')
                    ->middleware($middleware);
            }
        } else {
            $route->any('thinkadmin/:controller/:action', '\suframe\thinkAdmin\controller\:controller@:action');
            //单应用，全局配置middleware
            $this->app->middleware->import($middleware);
        }
    }

    /**
     * 路由中间件
     */
    protected function registerRouteMiddleware()
    {
        $this->routeMiddleware = config('thinkAdmin.routeMiddleware', []);
    }

    protected function initAdmin()
    {
        if ($this->app->runningInConsole()) {
            return false;
        }
        $this->app->bind('admin', Admin::class);
    }

    /**
     * 数据库迁移
     * @return bool
     */
    protected function createMigrations()
    {
        if (!$this->app->runningInConsole()) {
            return false;
        }
        $dataPath = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        if (!is_dir($dataPath)) {
            mkdir($dataPath, 0755, true);
        }
        $sqlDir = __DIR__ . '/database/migrations';
        foreach (new DirectoryIterator($sqlDir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $target = $dataPath . $fileInfo->getFilename();
            if (!file_exists($target)) {
                copy($fileInfo->getRealPath(), $target);
            }
        }

    }
}
