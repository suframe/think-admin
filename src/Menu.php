<?php

namespace suframe\thinkAdmin;

use ReflectionMethod;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminPermissions;
use suframe\thinkAdmin\traits\SingleInstance;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class Menu
{

    use SingleInstance;

    public function installAnnotation($class, $fileParentMenuId = 0, $app_name = '')
    {
        try {
            $installMenu = null;
            $objRef = new \ReflectionClass($class);
            if ($doc = $objRef->getDocComment()) {
                $doc = $this->parseDoc($doc);
                if (isset($doc['menu']) && $doc['menu']) {
                    //类注释的菜单(二级)
                    $installMenu = $this->installMenu($fileParentMenuId, $doc, $class, null, $app_name);
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
                $installMenu = $this->installMenu($fileParentMenuId, $doc, $class, $method, $app_name);
                //增加权限
                $this->installPermissions($doc, $class, $installMenu, $method, $app_name);
            }

        } catch (\ReflectionException $e) {
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
     * 安装菜单
     * @param $parentMenuId
     * @param $doc
     * @param $class
     * @param ReflectionMethod $method
     * @param string $app_name
     * @return AdminMenu|bool
     */
    public function installMenu($parentMenuId, $doc, $class, ReflectionMethod $method = null, $app_name = '')
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
            $parentMenuId,
            null,
            $app_name
        );
    }

    /**
     * 菜单如数据库
     * @param $title
     * @param $uri
     * @param int $pid
     * @param null $icon
     * @param string $app_name
     * @return array|AdminMenu|\think\Model|null
     */
    public function insertMenu($title, $uri, $pid = 0, $icon = null, $app_name = '')
    {
        $menuInfo = [
            'title' => $title,
            'uri' => $uri,
            'parent_id' => $pid,
            'app_name' => $app_name,
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
     * 安装权限
     * @param $doc
     * @param $class
     * @param array $installMenu
     * @param ReflectionMethod|null $method
     * @param string $app_name
     * @return bool
     */
    protected function installPermissions($doc, $class, $installMenu = [], ReflectionMethod $method = null, $app_name = '')
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
            $info['app_name'] = $app_name;
            $data[] = $info;
        }

        if ($permissions && $data) {
            AdminPermissions::insertAll($data);
        }
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

}