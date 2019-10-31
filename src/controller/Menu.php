<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\MenuForm;
use suframe\thinkAdmin\ui\table\MenuTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

/**
 * 管理员菜单
 * Class Menu
 * @package suframe\thinkAdmin\controller
 */
class Menu extends SystemBase
{
    protected $urlPre = '/thinkadmin/menu/';
    use CURDController;

    private function curlInit(){
        $this->currentNav = 'menu';
        $this->currentNavZh = '菜单';
    }

    private function getManageModel()
    {
        return AdminMenu::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel()::order('id', 'desc'), [
            'title' => 'like', 'uri' => 'like'
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
        $form->setRuleByClass(MenuForm::class);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table){
        $table->createByClass(MenuTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
    }

}