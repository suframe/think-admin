<?php

namespace suframe\thinkAdmin\controller;

use suframe\form\Form;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\ui\form\AdminUserForm;
use suframe\thinkAdmin\ui\table\UserTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

class User extends SystemBase
{

    public function index()
    {
        if($this->request->isAjax()){
            $users = AdminUsers::field([
                'id', 'username', 'real_name', 'create_time', 'avatar'
            ])->order('id', 'desc');
            $rs = $this->parseSearchWhere($users, [
                'username' => 'like',
                'create_time' => 'betweenTime',
            ]);
            return json_return($rs);
        }

        $table = new UITable();
        $table->createByClass(UserTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlA('user/add')]);

        $this->setNav('user');
        View::assign('table', $table);
        return View::fetch('common/table');
    }


    /**
     * 新增
     * @throws \Exception
     */
    public function add()
    {
        if($this->request->isPost()){
            $menu = new AdminUsers();
            $menu->username = $this->request->post('username');
            $menu->password = Admin::auth()->hashPassword($this->requirePostInt('password'));
            $menu->real_name = $this->requirePost('real_name');
            $menu->avatar = $this->request->param('avatar');
            $rs = $menu->save();
            return $this->handleResponse($rs);
        }
        $this->setNav('user');
        $form = (new Form)->createElm();
        $form->setData($this->request->port());
        $form->setRuleByClass(AdminUserForm::class);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('common/form');
    }

    /**
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function base()
    {
        $this->setNav('base');
        $admin = $this->getAdminUser();
        $fields = [
            'real_name',
            'avatar',
        ];
        if ($this->request->isAjax() && $this->request->post()) {
            $post = $this->request->post();
            $rs = $admin->allowField($fields)->save($post);
            return $this->handleResponse($rs);
        }
        $admin = $this->getAdminUser();
        $form = (new Form)->createElm();
        $form->setData($admin->toArray());
        $form->setRuleByClass(AdminUserForm::class, [], $fields);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('user/base');
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
        /** @var AdminUsers $admin */
        $admin = AdminUsers::find($id);
        if (!$admin) {
            throw new \Exception('管理员不存在');
        }
        if ($admin->isSupper()) {
            throw new \Exception('超级管理员不允许删除');
        }
        return $this->handleResponse($admin->delete(), '删除成功');
    }
}