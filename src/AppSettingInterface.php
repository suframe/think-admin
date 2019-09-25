<?php

namespace suframe\thinkAdmin;

use app\BaseController;
use DirectoryIterator;
use think\Collection;
use think\facade\Db;

/**
 * 应用配置基类
 * Class AppSettingInterface
 * @package suframe\thinkAdmin
 */
abstract class AppSettingInterface
{

    abstract function info();
    abstract function remove();

    /**
     * @return bool
     * @throws \Exception
     */
    public function install()
    {
        //检测
        $app = $this->check();

        Db::startTrans();
        try {
            //初始化菜单
            $this->installMenu();
            //数据库
            $this->installDb();
            //更新状态
            $app->installed = 1;
            $app->save();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    /**
     * @return array|\think\Model|null
     * @throws \Exception
     */
    protected function check()
    {
        $app = $this->getApp();
        if ($app->installed === 1) {
//            throw new \Exception('应用已安装');
        }
        return $app;
    }

    /**
     * 安装菜单
     * @throws \Exception
     */
    protected function installMenu($dirName = '')
    {
        $ref = new \ReflectionClass($this);
        $dir = dirname($ref->getFileName());
        $controller_layer = config('route.controller_layer');
        $controllerDir = $dir . DIRECTORY_SEPARATOR
            . $controller_layer . DIRECTORY_SEPARATOR
            . $dirName;

        if(!is_dir($controllerDir)){
            throw new \Exception('controller dir not found');
        }
        foreach (new DirectoryIterator($controllerDir) as $fileInfo) {
            $fileName = $fileInfo->getFilename();
            if ($fileInfo->isDot()) {
                continue;
            }
            if(is_dir($fileName)){
                $this->installMenu($fileName);
            }
            if('php' != $fileInfo->getExtension()){
                continue;
            }
            $class = $ref->getNamespaceName() . "\\{$controller_layer}\\" . ucfirst(substr($fileName, 0, strlen($fileName) - 4));
            if(!class_exists($class)){
                continue;
            }

            $objRef = new \ReflectionClass($class);
            foreach ($objRef->getMethods() as $method) {
                if(!$method->isPublic()){
                    continue;
                }
                $doc = $this->parseDoc($method->getDocComment());
                echo '<pre>';
                    print_r($doc);
                echo '<pre>';exit;
            }
            echo '<pre>';
            print_r($objRef->getMethods());
            echo '<pre>';exit;
        }

    }

    protected function parseDoc($docComment)
    {
        $docs = explode("\n", $docComment);
        array_shift($docs);
        array_pop($docs);
        $rs = [];
        foreach ($docs as $doc) {
            if(strpos($doc, '* @') === false){
                continue;
            }
            $doc = substr($doc, strpos($doc, '* @') + 3, strlen($doc));
            if(!$doc){
                continue;
            }
            $splitPos = strpos($doc, ' ');
            $name = substr($doc, 0, $splitPos);
            $desc = rtrim(substr($doc, $splitPos + 1, strlen($doc)));
            $rs[$name] = $desc;
        }
        echo '<pre>';
            print_r($rs);
        echo '<pre>';exit;
        $rs = [];
        return new Collection($rs);
    }

    /**
     * 数据库安装
     */
    protected function installDb()
    {
    }

    /**
     * @return array|\think\Model|null
     * @throws \Exception
     */
    protected function getApp()
    {
        $name = $this->info()['app_name'];
        $app = Admin::apps()->find($name);
        if (!$app) {
            throw new \Exception('app not found, please check new apps.');
        }
        return $app;
    }

}