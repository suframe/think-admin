<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\model\AdminRoleUsers;

class Menu extends Base
{

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function all()
    {
        $list = AdminMenu::order('order', 'asc')
            ->order('id', 'asc')
            ->select()
            ->toArray();
        return json_return($list);
    }

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function my()
    {
        $menus = Admin::auth()->getAdminMenu();
        return json_return($menus);
    }

    /**
     * 新增菜单
     * @throws \Exception
     */
    public function add()
    {
        $menu = new AdminMenu;
        $menu->parent_id = $this->request->post('parent_id');
        $menu->order = $this->requirePostInt('order');
        $menu->title = $this->requirePost('title');
        $menu->icon = $this->requirePost('icon');
        $menu->uri = $this->requirePost('uri');
        $menu->permission = $this->requirePost('permission');
        return $menu->save();
    }

    /**
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function edit()
    {
        $id = $this->requirePostInt('id');
        $post = $this->request->post();
        $menu = AdminMenu::find($id);
        if (!$menu) {
            throw new \Exception('menu not exist');
        }
        return $menu->allowField([
            'parent_id',
            'order',
            'title',
            'icon',
            'uri',
            'permission'
        ])->save($post);

    }

    /**
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function delete()
    {
        $id = $this->requirePostInt('id');
        $menu = AdminMenu::find($id);
        if (!$menu) {
            throw new \Exception('menu not exist');
        }
        return $menu->delete();
    }
}