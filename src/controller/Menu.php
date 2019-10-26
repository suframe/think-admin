<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\model\AdminRoleUsers;
use suframe\thinkAdmin\ui\table\MenuTable;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

/**
 * 管理员菜单
 * Class Menu
 * @package suframe\thinkAdmin\controller
 */
class Menu extends SystemBase
{

    /**
     * @return string|\think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        if($this->request->isAjax()){
            $rs = $this->parseSearchWhere(AdminMenu::order('id', 'desc'), [
                'title' => 'like', 'uri' => 'like'
            ]);
            return json_return($rs);
        }

        $table = new UITable();
        $table->setEditOps(url('/thinkadmin/menu/eidt'), ['id']);
        $table->setDeleteOps(url('/thinkadmin/menu/delete'), ['id']);
        $table->createByClass(MenuTable::class);
        $this->setNav('menu');
        View::assign('table', $table);
        return View::fetch('common/table');
    }

    /**
     * 所有菜单
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
     * 我的菜单
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
     * @return \think\response\Json
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
        return $this->handleResponse($menu->delete(), '删除成功', '删除失败');
    }
}