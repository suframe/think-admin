<?php

namespace suframe\thinkAdmin;

use DirectoryIterator;
use ReflectionMethod;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminAppsUser;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminPermissions;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\model\AdminRolePermissions;
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
     * 卸载
     * @throws \Exception
     */
    public function uninstall(AdminApps $adminApps)
    {
        //删除授权用户
        AdminAppsUser::where('app_id', $adminApps->id)->delete();
        //删除菜单
        $adminMenuQuery = AdminMenu::where('app_name', $adminApps->app_name);
        $menuIds = $adminMenuQuery->column('id');
        $menuIds && AdminRoleMenu::whereIn('menu_id', $menuIds)->delete();
        $adminMenuQuery->delete();
        //删除权限
        $adminPermissionQuery = AdminPermissions::where('app_name', $adminApps->app_name);
        $permissionIds = $adminPermissionQuery->column('id');
        $permissionIds && AdminRolePermissions::whereIn('permission_id', $permissionIds)->delete();
        $adminPermissionQuery->delete();
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

    protected $app_name;

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
        $this->app_name = $info['app_name'];
        $installMenu = Admin::menu()->insertMenu(
            $info['menu_title'] ?? $info['title'],
            $info['entry'],
            0,
            $info['menu_icon'] ?? 'el-icon-apple',
            $this->app_name,
            $info['show_menu'] ?? 2
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
     * 安装配置权限
     */
    protected function installConfigPermissions()
    {
        $permission = $this->permission();
        foreach ($permission as $item) {
            if (!isset($item['slug']) || AdminPermissions::getBySlug($item['slug'])) {
                continue;
            }
            $item['app_name'] = $this->app_name;
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
        $namespaceName = $ref->getNamespaceName() . "\\{$controller_layer}\\";

        foreach (new DirectoryIterator($controllerDir) as $fileInfo) {
            $fileParentMenuId = $parentMenuId;
            $fileName = $fileInfo->getFilename();
            if ($fileInfo->isDot()) {
                continue;
            }
            if (is_dir($fileName)) {
                $this->installAnnotation($parentMenuId, $app, $fileName);
            }
            if ('php' != $fileInfo->getExtension()) {
                continue;
            }
            $class = $namespaceName . ucfirst(substr($fileName, 0,
                    strlen($fileName) - 4));
            if (!class_exists($class)) {
                continue;
            }

            Admin::menu()->installAnnotation($class, $fileParentMenuId, $this->app_name);
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
            $installMenu = Admin::menu()->insertMenu(
                $menu['title'],
                $menu['uri'] ?? '',
                $parentMenuId,
                $menu['icon'] ?? '',
                $this->app_name,
                $info['show_menu'] ?? 1
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
            if (AdminPermissions::getBySlug($info['slug'])) {
                continue;
            }
            $info['app_name'] = $this->app_name;
            $data[] = $info;
        }

        if ($permissions && $data) {
            AdminPermissions::insertAll($data);
        }
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