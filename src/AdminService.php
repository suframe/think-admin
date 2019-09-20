<?php

namespace suframe\thinkAdmin;

use DirectoryIterator;
use think\File;
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

    protected function createMigrations()
    {
        if(!$this->app->runningInConsole()){
            return false;
        }
        $dataPath = $this->app->getRootPath() . 'database' . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        if(!is_dir($dataPath)){
            mkdir($dataPath, 0755, true);
        }
        $sqlDir = __DIR__ . '/database/migrations';
        foreach (new DirectoryIterator($sqlDir) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $target = $dataPath . $fileInfo->getFilename();
            if(file_exists($target)){
                return false;
            }
            copy($fileInfo->getRealPath(), $target);
        }

    }
}
