<?php

namespace suframe\thinkAdmin;

use DirectoryIterator;
use ReflectionMethod;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminPermissions;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
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
            $this->installMenuAndPermissions($app);
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
     * @return AdminApps
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
     * @param AdminApps $app
     * @param string $dirName
     * @return bool
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function installMenuAndPermissions(AdminApps $app, $dirName = '')
    {
        //配置菜单
        $info = $this->info();
        $installMenu = $this->insertMenu(
            $info['menu_title'] ?? $info['title'],
            $info['entry'],
            0,
            $info['menu_icon'] ?? 'el-icon-apple'
        );
        if (!$installMenu) {
            throw new \Exception('应用没有配置入口');
        }
        $parentMenuId = $installMenu->id;
        $menus = $this->menu();
        if ($menus) {
            $this->installConfigMenu($menus, $parentMenuId);
        }
        $this->installConfigPermissions();
        $this->installAnnotation($parentMenuId, $app, $dirName);
        return true;
    }

    /**
     * 菜单如数据库
     * @param $title
     * @param $uri
     * @param int $pid
     * @param null $icon
     * @return array|AdminMenu|\think\Model|null
     */
    protected function insertMenu($title, $uri, $pid = 0, $icon = null)
    {
        $menuInfo = [
            'title' => $title,
            'uri' => $uri,
            'parent_id' => $pid,
        ];
        if ($icon) {
            $menuInfo['icon'] = $icon;
        }
        try {
            $menu = AdminMenu::where($menuInfo)->find();
            if ($menu) {
                return $menu;
            }
        } catch (DataNotFoundException|ModelNotFoundException|DbException $e) {
        }
        return AdminMenu::create($menuInfo);
    }

    /**
     * 安装菜单
     * @param $parentMenuId
     * @param $doc
     * @param $class
     * @param ReflectionMethod $method
     * @return AdminMenu|bool
     */
    protected function installMenu($parentMenuId, $doc, $class, ReflectionMethod $method = null)
    {
        if (!isset($doc['menu'])) {
            return false;
        }
        $menu = $doc['menu'];
        if (strpos($menu, '{') === false) {
            $title = trim($menu);
            $uri = $this->getUriByClass($class);
            if ($method) {
                $uri .= '/' . lcfirst($method->getName());
            }
        } else {
            $menu = json_decode($menu, true);
            $title = $menu['title'] ?? '';
            $uri = $menu['uri'] ?? '';
        }

        return $this->insertMenu(
            $title,
            $uri,
            $parentMenuId
        );
    }

    /**
     * 安装配置权限
     */
    protected function installConfigPermissions()
    {
        $permission = $this->permission();
        foreach ($permission as $item) {
            if (!isset($item['slug']) || AdminPermissions::getBySlug($item['slug'])) {
                continue;
            }
            AdminPermissions::insert($item);
        }
    }

    /**
     * 安装注解菜单和权限
     * @param $parentMenuId
     * @param AdminApps $app
     * @param string $dirName
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function installAnnotation($parentMenuId, AdminApps $app, $dirName = '')
    {
        $ref = new \ReflectionClass($this);
        $dir = dirname($ref->getFileName());
        $controller_layer = config('route.controller_layer');
        $controllerDir = $dir . DIRECTORY_SEPARATOR
            . $controller_layer . DIRECTORY_SEPARATOR
            . $dirName;

        if (!is_dir($controllerDir)) {
            throw new \Exception('controller dir not found');
        }
        //注解菜单
        foreach (new DirectoryIterator($controllerDir) as $fileInfo) {
            $fileParentMenuId = $parentMenuId;
            $fileName = $fileInfo->getFilename();
            if ($fileInfo->isDot()) {
                continue;
            }
            if (is_dir($fileName)) {
                $this->installMenuAndPermissions($app, $fileName);
            }
            if ('php' != $fileInfo->getExtension()) {
                continue;
            }
            $class = $ref->getNamespaceName() . "\\{$controller_layer}\\" . ucfirst(substr($fileName, 0,
                    strlen($fileName) - 4));
            if (!class_exists($class)) {
                continue;
            }
            $installMenu = null;
            $objRef = new \ReflectionClass($class);
            if ($doc = $objRef->getDocComment()) {
                $doc = $this->parseDoc($doc);
                if (isset($doc['menu']) && $doc['menu']) {
                    //类注释的菜单(二级)
                    $installMenu = $this->installMenu($fileParentMenuId, $doc, $class);
                    if ($installMenu) {
                        $fileParentMenuId = $installMenu->id;
                    }
                }
                //增加权限
                $this->installPermissions($doc, $class, $installMenu);
            }
            foreach ($objRef->getMethods() as $method) {
                if (!$method->isPublic()) {
                    continue;
                }
                $doc = $this->parseDoc($method->getDocComment());
                //method注释的菜单(二/三级)
                $installMenu = $this->installMenu($fileParentMenuId, $doc, $class, $method);
                //增加权限
                $this->installPermissions($doc, $class, $installMenu, $method);
            }
        }
    }

    /**
     * 安装本地菜单配置
     * @param $menus
     * @param int $parentMenuId
     * @return int|mixed
     * @throws \Exception
     */
    protected function installConfigMenu($menus, $parentMenuId)
    {
        foreach ($menus as $menu) {
            $installMenu = $this->insertMenu(
                $menu['title'],
                $menu['uri'] ?? '',
                $parentMenuId,
                $menu['icon'] ?? ''
            );
            if (!$installMenu) {
                continue;
            }
            $installMenu->id;
            if (isset($menu['child'])) {
                $this->installConfigMenu($menu['child'], $installMenu->id);
            }
        }
        return true;
    }

    protected $uriClassStore = [];

    protected function getUriByClass($class)
    {
        if (isset($this->uriClassStore[$class])) {
            return $this->uriClassStore[$class];
        }
        $uri = str_replace([
            '\\' . app()->getNamespace() . '\\',
            '\\' . config('route.controller_layer') . '\\'
        ], '\\', '\\' . $class);
        $uri = str_replace('\\', '/', $uri);
        $uri = explode('/', ltrim($uri, '/'));
        $uri = array_map(function ($value) {
            return lcfirst($value);
        }, $uri);
        $uri = '/' . implode('/', $uri);
        $this->uriClassStore[$class] = $uri;
        return $uri;
    }

    /**
     * 安装权限
     * @param $doc
     * @param $class
     * @param array $installMenu
     * @param ReflectionMethod|null $method
     * @return bool
     */
    protected function installPermissions($doc, $class, $installMenu = [], ReflectionMethod $method = null)
    {
        $permissions = [];
        if (isset($doc['permissions'])) {
            $permissions = json_decode($doc['permissions'], true);
        }
        if (isset($doc['permission'])) {
            if (strpos($doc['permission'], '{') !== false) {
                $permissions[] = json_decode($doc['permission'], true);
            } else {
                $permissions[] = $doc['permission'];
            }
        }
        if (!$permissions) {
            return false;
        }
        $data = [];
        foreach ($permissions as $permission) {
            if (!is_array($permission) && $permission === '*') {
                $info['http_method'] = AdminPermissions::$methods['*'];
            } else {
                $info = $permission;
            }
            if (!isset($info['http_path'])) {
                $info['http_path'] = $this->getUriByClass($class);
                if ($method) {
                    $info['http_path'] .= '/' . lcfirst($method->getName());
                } else {
                    $info['http_path'] .= '/*';
                }
            }
            if (!isset($info['name'])) {
                if ($installMenu) {
                    $info['name'] = $installMenu['title'];
                } else {
                    $info['name'] = $info['http_path'];
                }
            }
            if (!isset($info['slug'])) {
                $info['slug'] = $info['http_path'];
            }
            $data[] = $info;
        }

        if ($permissions) {
            AdminPermissions::insertAll($data);
        }
    }

    protected function parseDoc($docComment)
    {
        $docs = explode("\n", $docComment);
        array_shift($docs);
        array_pop($docs);
        $rs = [];
        foreach ($docs as $doc) {
            if (strpos($doc, '* @') === false) {
                continue;
            }
            $doc = substr($doc, strpos($doc, '* @') + 3, strlen($doc));
            if (!$doc) {
                continue;
            }
            $splitPos = strpos($doc, ' ');
            $name = substr($doc, 0, $splitPos);
            $desc = rtrim(substr($doc, $splitPos + 1, strlen($doc)));
            $rs[$name] = $desc;
        }
        return $rs;
    }

    /**
     * 数据库安装
     */
    protected function installDb()
    {
    }

    /**
     * @return AdminApps
     * @throws \Exception
     */
    protected function getApp()
    {
        $name = $this->info()['app_name'];
        /** @var AdminApps $app */
        $app = Admin::apps()->find($name);
        if (!$app) {
            throw new \Exception('app not found, please check new apps.');
        }
        return $app;
    }

    public function menu()
    {
        return [];
    }

    public function permission()
    {
        return [];
    }
}