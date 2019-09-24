<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminUsers;

class User extends Base
{

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
        if($rs){
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
        $user = AdminUsers::find($id);
        if (!$user) {
            throw new \Exception('admin not exist');
        }
        if(isset($post['password'])){
            $post['password'] = Admin::auth()->hashPassword($post['password']);
        }
        return $user->allowField([
            'real_name',
            'password',
            'avatar',
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
        $admin = AdminUsers::find($id);
        if (!$admin) {
            throw new \Exception('admin not exist');
        }
        return $admin->delete();
    }
}