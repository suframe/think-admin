<?php

namespace suframe\thinkAdmin\controller;

use suframe\form\facade\Form;
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
            $params = $this->request->get([
                'id'
            ]);
            $users = AdminUsers::where($params)->field([
                'id', 'username', 'real_name', 'create_time', 'avatar'
            ]);
            if($username = $this->request->get('username')){
                $users->whereLike('username', "%{$username}%");
            }
            if($real_name = $this->request->get('real_name')){
                $users->whereLike('real_name', "%{$real_name}%");
            }
            if($create_time = $this->request->get('create_time')){
                $users->whereLike('create_time', "{$create_time}%");
            }
            if($create_times = $this->request->get('create_times')){
                $users->whereBetweenTime('create_time', $create_times[0], $create_times[1]);
            }
            return json_return($users->select());
        }

        $table = new UITable();
        $table->createByClass(UserTable::class);
        $this->setNav('user');
        View::assign('table', $table);
        return View::fetch('user/index');
    }

    /**
     * 我的信息
     * @return \think\response\Json
     */
    public function info()
    {
        $rs = Admin::user()->info();
        return json_return($rs);
    }

    /**
     * 通过id查找管理员
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function find()
    {
        $id = $this->requireParamInt('id');
        /** @var AdminUsers $rs */
        $rs = AdminUsers::find($id);
        if ($rs) {
            $rs = $rs->info();
        }
        return json_return($rs);
    }

    /**
     * 搜索
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function search()
    {
        list($page, $nums) = $this->requestPage();
        return AdminUsers::order('id', 'desc')
            ->field('id,username,real_name,avatar,create_time,update_time,login_fail')
            ->page($page, $nums)
            ->select();
    }

    /**
     * 新增
     * @throws \Exception
     */
    public function add()
    {
        $menu = new AdminUsers();
        $menu->username = $this->request->post('username');
        $menu->password = Admin::auth()->hashPassword($this->requirePostInt('password'));
        $menu->real_name = $this->requirePost('real_name');
        $menu->avatar = $this->request->param('avatar');
        return $menu->save();
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
        $form = Form::createElm();
        $form->setData($admin->toArray());
        $form->setRuleByClass(AdminUserForm::class, [], $fields);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('user/base');
    }

    /**
     * 修改密码
     * @return string|\think\response\Json
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function password()
    {
        $this->setNav('password');
        if ($this->request->isAjax() && $this->request->post()) {
            $password = $this->request->post('password');
            $password_confirm = $this->request->post('password_confirm');
            if (!$password) {
                return $this->handleResponse(false, '请输入密码');
            }
            if ($password != $password_confirm) {
                return $this->handleResponse(false, '密码不一致');
            }
            $auth = Admin::auth();
            $score = $auth->judgePassword($password);
            if ($score < config('thinkAdmin.auth.judgePassword', 2)) {
                return $this->handleResponse(false, '密码不安全');
            }
            $admin = $this->getAdminUser();
            $admin->password = Admin::auth()->hashPassword($password);
            return $this->handleResponse($admin->save());
        }
        $form = Form::createElm();
        $form->setRuleByClass(AdminUserForm::class, [], ['password', 'password_confirm']);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('user/base');
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
        /** @var AdminUsers $admin */
        $admin = AdminUsers::find($id);
        if (!$admin) {
            throw new \Exception('管理员不存在');
        }
        if ($admin->isSupper()) {
            throw new \Exception('超级管理员不允许删除');
        }
        return $admin->delete();
    }
}