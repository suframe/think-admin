<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\MenuForm;
use suframe\thinkAdmin\ui\table\MenuTable;
use suframe\thinkAdmin\ui\UITable;

/**
 * 管理员菜单
 * Class Menu
 * @package suframe\thinkAdmin\controller
 */
class Menu extends SystemBase
{
    protected $urlPre = '/thinkadmin/menu/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'menu';
        $this->currentNavZh = '菜单';
    }

    private function getManageModel()
    {
        return AdminMenu::class;
    }

    private function ajaxSearch()
    {
        $parent_id = $this->request->param('parent_id', 0);
        $rs = $this->parseSearchWhere(
            $this->getManageModel()::where('parent_id', $parent_id),
            ['title' => 'like', 'uri' => 'like']
        )->append(['app_name_zh', 'has_child']);
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
    private function getTableSetting($table)
    {
        $table->createByClass(MenuTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);

        $parent_id = $this->request->param('parent_id');
        if ($parent_id != null) {
            if ($parent_id) {
                $parent = AdminMenu::find($parent_id);
                $parent && $table->setBreadcrumb('上一级', $this->urlABuild('index', ['parent_id' => $parent['parent_id']]));
                $table->setBreadcrumb('子菜单');
            }
        }
    }

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function roleMenu()
    {
        $role_id = $this->requireParamInt('role_id');
        $all = AdminMenu::buildOptions('all', false, 'key');
        $my = AdminRoleMenu::where('role_id', $role_id)->field('menu_id')->select()->column('menu_id');
        $rs = [
            'all' => $all,
            'my' => $my,
        ];
        return json_return($rs);
    }


    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function roleMenuTree()
    {
        $role_id = $this->requireParamInt('role_id');
        $all = AdminMenu::buildTree();
        $my = AdminRoleMenu::where('role_id', $role_id)->field('menu_id')->select()->column('menu_id');
        $rs = [
            'all' => $all,
            'my' => $my,
        ];
        return json_return($rs);
    }


}