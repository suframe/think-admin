<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminRoleMenu extends Model
{

    /**
     * @param AdminUsers $user
     * @param bool $withApp
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getMenuByUser(AdminUsers $user, $withApp = true, $app_name = '')
    {
        if ($user->isSupper()) {
            $menu_ids = 'all';
        } else {
            $user_id = $user->id;
            $role_ids = AdminRoleUsers::getRoleByUser($user_id);
            if (!$role_ids) {
                return [];
            }
            $menu_ids = AdminRoleMenu::whereIn('role_id', $role_ids)
                ->field('menu_id')
                ->column('menu_id');
            if (!$menu_ids) {
                return [];
            }
        }
        $adminMenu = AdminMenu::order('order', 'desc');
        $adminMenu->where('show_menu', 1);
        if ($menu_ids !== 'all') {
            $adminMenu->whereIn('id', $menu_ids);
        }
        if (!$withApp) {
            $adminMenu->whereNull('app_name');
        }

        if($app_name) {
            $adminMenu->where('app_name', $app_name);
        }
        $rs = $adminMenu->select()->toArray();
        if (!$rs) {
            return [];
        }
        //组织成树形
        return static::buildTree($rs);
    }

    public static function buildTree($menus, $pid = 0)
    {
        $rs = [];
        foreach ($menus as $key => $menu) {
            if ($menu['parent_id'] == $pid) {
                $isUrl = (strpos($menu['uri'], 'http') === 0) ||
                    (strpos($menu['uri'], '//') === 0);
                $rs[$key] = [
                    'title' => $menu['title'],
                    'uri' => $isUrl ? $menu['uri'] : url($menu['uri'])->build(),
                    'icon' => $menu['icon'],
                ];
                unset($menus[$key]);
                //查找子类
                $child = static::buildTree($menus, $menu['id']);
                if ($child) {
                    $rs[$key]['child'] = $child;
                }
            }
        }
        return $rs;
    }
}