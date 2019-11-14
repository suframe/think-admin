<?php

namespace suframe\thinkAdmin\controller;

use FormBuilder\Exception\FormBuilderException;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\model\AdminRoleUsers;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminUserForm;
use suframe\thinkAdmin\ui\table\UserTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

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
     * 管理员角色
     * @throws \Exception
     */
    public function roles()
    {
        $id = $this->requireParamInt('id');
        if ($this->request->isAjax() && $this->request->isPost()) {
            $direction = $this->requirePost('direction');
            $movedKeys = $this->requirePost('movedKeys');
            if ($direction == 'right') {
                //增加
                $data = [];
                foreach ($movedKeys as $movedKey) {
                    $data[] = [
                        'user_id' => $id,
                        'role_id' => $movedKey,
                    ];
                }
                $rs = AdminRoleUsers::insertAll($data);
            } else {
                $rs = AdminRoleUsers::where('user_id', $id)->whereIn('role_id', $movedKeys)->delete();
            }
            return $this->handleResponse($rs);
        }
        $this->setNav('user');
        View::assign('id', $id);
        View::assign('pageTitle', '管理员权限编辑');
        return View::fetch('user/roles');
    }

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function myRoles()
    {
        $user_id = $this->requireParamInt('user_id');
        $all = AdminRoles::buildOptions();
        $my = AdminRoleUsers::where('user_id', $user_id)->field('role_id')->select()->column('role_id');
        $rs = [
            'all' => $all,
            'my' => $my,
        ];
        return json_return($rs);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table){
        $table->createByClass(UserTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
        $configRole = [
            'type' => 'link',
            'label' => '角色',
            'icon' => 'el-icon-menu',
            'url' => $this->urlA('roles'),
            'vars' => ['id'],
        ];
        $table->setOps('roles', $configRole);
        $table->setConfigs('opsWidth', 180);
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