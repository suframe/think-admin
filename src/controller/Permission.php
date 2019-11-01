<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminPermissions;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminPermissionForm;
use suframe\thinkAdmin\ui\table\PermissionTable;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\Cache;
use think\facade\View;

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

}