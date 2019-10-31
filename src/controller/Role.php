<?php
namespace suframe\thinkAdmin\controller;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminRoleForm;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;

class Role extends SystemBase
{
    protected $urlPre = '/thinkadmin/role/';
    use CURDController;

    private function curlInit(){
        $this->currentNav = 'role';
        $this->currentNavZh = '角色';
    }

    private function getManageModel()
    {
        return AdminRoles::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel()::order('id', 'desc'), [
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
    private function getTableSetting($table){
        $table->createByClass(RoleTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
    }
}