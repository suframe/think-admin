<?php

namespace suframe\thinkAdmin;

use DirectoryIterator;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\traits\SingleInstance;
use think\Collection;
use think\Exception;

class Apps
{
    use SingleInstance;

    public function checkNewApp()
    {
        $dir = app()->getBasePath();
        $exclude = [
            'controller',
            'middleware',
            'model',
            'validate'
        ];
        $apps = [];
        $appsExist = AdminApps::order('order', 'asc')->column('app_name');
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            $fileName = $fileInfo->getFilename();
            if ($fileInfo->isDot() ||
                !$fileInfo->isDir() ||
                in_array($fileName, $exclude)
            ) {
                continue;
            }
            $class = "\\app\\{$fileName}\\Setting";
            if (!class_exists($class)) {
                continue;
            }
            $info = (new $class)->info();
            if (in_array($info['app_name'], $appsExist)) {
                continue;
            }
            $apps[$info['app_name']] = $info;
        }

        if (!$apps) {
            throw new \Exception('未检测到新应用');
        }
        foreach ($apps as $app) {
            $newApp = new AdminApps();
            $newApp->save($app);
        }
        return true;
    }

    /**
     * 安装
     * @param $appName
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function install($appName)
    {
        $app = AdminApps::where('app_name', $appName)->find();
        if (!$app) {
            throw new Exception('app not found');
        }
        $class = $app['setting_class'];
        return (new $class)->install();
    }

    /**
     * 卸载
     * @param $appName
     * @return mixed
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function remove($appName)
    {
        $app = AdminApps::where('app_name', $appName)->find();
        if (!$app) {
            throw new Exception('app not found');
        }
        $app->installed = 0;
        return $app->save();
    }

    /**
     * 删除
     * 为了安全，不删除文件
     * @param $appName
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function delete($appName)
    {
        $app = AdminApps::where('app_name', $appName)->find();
        if (!$app) {
            throw new Exception('app not found');
        }
        return $app->delete();
    }

    public function find($appName)
    {
        return AdminApps::where('app_name', $appName)->find();
    }

}