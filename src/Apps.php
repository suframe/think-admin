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
            $apps[$info['app_name']]['setting_class'] = $class;
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
     * @throws \Exception
     */
    public function install($appName)
    {
        /** @var AdminApps $app */
        $app = AdminApps::where('app_name', $appName)->find();
        if (!$app) {
            throw new Exception('应用未找到');
        }
        if ($app->isInstalled()) {
            throw new Exception('应用已安装过，请勿重复安装');
        }
        $class = $app['setting_class'];
        if (!$class || !class_exists($class)) {
            throw new \Exception($class . "配置类不存在");
        }
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