<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminSetting;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminSettingForm;
use suframe\thinkAdmin\ui\table\SettingTable;
use suframe\thinkAdmin\ui\UITable;

/**
 * 后台通用设置
 * Class Setting
 * @package suframe\thinkAdmin\controller
 */
class Setting extends SystemBase
{

    protected $urlPre = '/thinkadmin/setting/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'setting';
        $this->currentNavZh = '角色';
    }

    private function getManageModel()
    {
        return AdminSetting::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel(), [
            'name' => 'like', 'key' => 'like',
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
        $form->setRuleByClass(AdminSettingForm::class);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table)
    {
        $table->createByClass(SettingTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
    }

}