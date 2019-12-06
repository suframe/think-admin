<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\model\AdminRolePermissions;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\model\AdminRoleUsers;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminRoleForm;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

class Role extends SystemBase
{
    protected $urlPre = '/thinkadmin/role/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'role';
        $this->currentNavZh = '角色';
    }

    private function getManageModel()
    {
        return AdminRoles::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel(), [
            'name' => 'like',
        ]);
        return json_return($rs);
    }

    /**
     * @param \suframe\form\Form $form
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     */
    private function getFormSetting($form)
    {
        $form->setRuleByClass(AdminRoleForm::class);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table)
    {
        $table->createByClass(RoleTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
        $configMenu = [
            'type' => 'link',
            'label' => '菜单',
            'icon' => 'el-icon-menu',
            'url' => $this->urlA('menu'),
            'vars' => ['id'],
        ];
        $table->setOps('menu', $configMenu);
        $configPermissions = [
            'type' => 'link',
            'label' => '权限',
            'icon' => 'el-icon-check',
            'url' => $this->urlA('permissions'),
            'vars' => ['id'],
        ];
        $table->setOps('permissions', $configPermissions);
        $table->setConfigs('opsWidth', 240);
    }

    /**
     * @param \think\Model $model
     * @throws \Exception
     */
    private function beforeDelete($model)
    {
        $rs = AdminRoleUsers::where('role_id', $model->id)->count();
        if ($rs) {
            throw new \Exception('此分组下有:' . $rs . '用户，删除失败');
        }
        $rs = AdminRoleMenu::where('role_id', $model->id)->count();
        if ($rs) {
            throw new \Exception('此分组下有:' . $rs . '菜单，请先移除菜单，删除失败');
        }
        $rs = AdminRolePermissions::where('role_id', $model->id)->count();
        if ($rs) {
            throw new \Exception('此分组下有:' . $rs . '权限，请先移除权限，删除失败');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function menu()
    {
        $id = $this->requireParamInt('id');
        if ($this->request->isAjax() && $this->request->isPost()) {
            $checked = $this->requirePost('checked');
            $data_id = $this->requirePost('data_id');
            if ($checked == 'true') {
                //增加
                $rs = AdminRoleMenu::insert([
                    'role_id' => $id,
                    'menu_id' => $data_id,
                ]);
            } else {
                $rs = AdminRoleMenu::where('role_id', $id)->where('menu_id', $data_id)->delete();
            }
            return $this->handleResponse($rs);
        }
        $this->setNav('role');
        View::assign('id', $id);
        View::assign('pageTitle', '角色菜单编辑');
        return View::fetch('role/menu');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function permissions()
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
                        'role_id' => $id,
                        'permission_id' => $movedKey,
                    ];
                }
                $rs = AdminRolePermissions::insertAll($data);
            } else {
                $rs = AdminRolePermissions::where('role_id', $id)->whereIn('permission_id', $movedKeys)->delete();
            }
            return $this->handleResponse($rs);
        }
        $this->setNav('role');
        View::assign('id', $id);
        View::assign('pageTitle', '角色权限编辑');
        return View::fetch('role/permissions');
    }
}