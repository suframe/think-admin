<?php

namespace suframe\thinkAdmin\controller;

use FormBuilder\Exception\FormBuilderException;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminUserForm;
use suframe\thinkAdmin\ui\table\UserTable;
use suframe\thinkAdmin\ui\UITable;

class User extends SystemBase
{
    protected $urlPre = '/thinkadmin/user/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'user';
        $this->currentNavZh = '用户';
    }

    private function getManageModel()
    {
        return AdminUsers::class;
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table){
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->createByClass(UserTable::class);
    }

    /**
     * @param \suframe\form\Form $form
     * @throws FormBuilderException
     * @throws \ReflectionException
     */
    private function getFormSetting($form)
    {
        $form->setRuleByClass(AdminUserForm::class);
    }

    private function ajaxSearch()
    {
        $users = AdminUsers::field([
            'id',
            'username',
            'real_name',
            'create_time',
            'avatar'
        ])->order('id', 'desc');
        $rs = $this->parseSearchWhere($users, [
            'username' => 'like',
            'create_time' => 'betweenTime',
        ]);
        return json_return($rs);
    }

    /**
     * @return mixed
     */
    private function getUpdateInfo()
    {
        if ($id = $this->request->param('id')) {
            return $this->getManageModel()::field([
                'id',
                'username',
                'real_name',
                'avatar'
            ])->find($id);
        }
        return [];
    }


    /**
     * @param \think\Model  $info
     * @param $post
     * @return mixed
     * @throws \Exception
     */
    private function beforeSave($info, $post)
    {
        if(isset($post['id']) && $post['id']){
            $info->allowField([
                'password',
                'real_name',
                'avatar',
            ]);
        }
        $password = $this->request->param('password');
        if($password) {
            $password_confirm = $this->requirePost('password_confirm');
            if($password !== $password_confirm){
                throw new \Exception('两次密码不一致');
            }
            $post['password'] = Admin::auth()->hashPassword($password);
        } else {
            unset($post['password']);
        }
        throw new \Exception('不好意思，不能修改了现在');
        return $post;
    }

    /**
     * @param AdminUsers $model
     * @throws \Exception
     */
    private function beforeDelete($model)
    {
        if ($model->isSupper()) {
            throw new \Exception('超级管理员不允许删除');
        }
    }
}