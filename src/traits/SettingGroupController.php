<?php

namespace suframe\thinkAdmin\traits;

use suframe\thinkAdmin\ui\UITable;
use think\facade\View;
use think\Model;
use think\Paginator;
use think\Request;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\ui\table\SettingGroupTable;
use suframe\thinkAdmin\ui\form\AdminSettingGroupForm;
use suframe\thinkAdmin\model\AdminSettingGroup;
use suframe\thinkAdmin\model\AdminSetting;

/**
 * Trait SettingGroupController
 * @package suframe\thinkAdmin\traits
 * @property Request $request
 */
trait SettingGroupController
{

    use CURDController;

    private function getManageModel()
    {
        return AdminSettingGroup::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel());
        return json_return($rs);
    }

    /**
     * @param \suframe\form\Form $form
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     */
    private function getFormSetting($form)
    {
        $form->setRuleByClass(AdminSettingGroupForm::class);
    }

    /**
     * @param UITable $table
     */
    private function getTableSetting($table)
    {
        $table->createByClass(SettingGroupTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setEditOps($this->urlA('update'), ['id']);
        $table->setDeleteOps($this->urlA('delete'), ['id']);
    }

    /**
     * @param \think\Model $model
     * @throws \Exception
     */
    private function beforeDelete($model){
        //检测分组下面是否有配置
        $exist = AdminSetting::where('group_key', $model->key)->count();
        if($exist){
            throw new \Exception("请先移除此分类下的配置项");
        }
    }

    private function beforeSave($info, $post)
    {
        $post['app_name'] = $this->getSettingAppName();
        return $post;
    }

}