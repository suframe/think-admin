<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminPermissions;
use suframe\thinkAdmin\model\AdminRolePermissions;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminPermissionForm;
use suframe\thinkAdmin\ui\table\PermissionTable;
use suframe\thinkAdmin\ui\UITable;

class Permission extends SystemBase
{

    protected $urlPre = '/thinkadmin/permission/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'permission';
        $this->currentNavZh = '权限';
    }

    private function getManageModel()
    {
        return AdminPermissions::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel()::order('id', 'desc'), [
            'name' => 'like', 'slug' => 'like', 'http_path' => 'like'
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
        $form->setRuleByClass(AdminPermissionForm::class);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table)
    {
        $table->createByClass(PermissionTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
    }

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function rolePermission()
    {
        $role_id = $this->requireParamInt('role_id');
        $all = AdminPermissions::buildOptions();
        $my = AdminRolePermissions::where('role_id', $role_id)->field('permission_id')->select()->column('permission_id');
        $rs = [
            'all' => $all,
            'my' => $my,
        ];
        return json_return($rs);
    }

}