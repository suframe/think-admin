<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminAppsUser;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\ui\form\AdminAppsForm;
use suframe\thinkAdmin\ui\table\AppsTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\Db;
use think\facade\View;

class Apps extends SystemBase
{

    protected $urlPre = '/thinkadmin/apps/';
    use CURDController;

    private function curlInit()
    {
        $this->currentNav = 'apps';
        $this->currentNavZh = '应用';
    }

    private function getManageModel()
    {
        return AdminApps::class;
    }

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel(), [
            'name' => 'like'
        ])->append(['installedName', 'type_name']);
        return json_return($rs);
    }

    /**
     * 管理员角色
     * @throws \Exception
     */
    public function users()
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
                        'app_id' => $id,
                        'user_id' => $movedKey,
                    ];
                }
                $rs = AdminAppsUser::insertAll($data);
            } else {
                $rs = AdminAppsUser::where('app_id', $id)->whereIn('user_id', $movedKeys)->delete();
            }
            return $this->handleResponse($rs);
        }
        $this->setNav('apps');
        View::assign('id', $id);
        View::assign('pageTitle', '应用授权');
        return View::fetch('apps/users');
    }

    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function userSearch()
    {
        $app_id = $this->requireParamInt('app_id');
        $all = AdminUsers::buildOptions();
        $my = AdminAppsUser::where('app_id', $app_id)->field('user_id')->select()->column('user_id');
        $rs = [
            'all' => $all,
            'my' => $my,
        ];
        return json_return($rs);
    }


    /**
     * @param UITable $table
     */
    private function getTableSetting($table)
    {
        $table->createByClass(AppsTable::class);
        $table->setButtons('add', ['title' => '增加', 'url' => $this->urlABuild('update')]);
        $table->setButtons('checkNewApp', ['title' => '检测新应用', 'url' => $this->urlABuild('checkNewApp'), 'isAjax' => true]);
        $table->setDeleteOps($this->urlA('delete'), ['id'], ['confirm' => '应用数据将一起被删除，确认删除?']);
        $table->setEditOps($this->urlA('update'), ['id']);
        $configUsers = [
            'type' => 'link',
            'label' => '授权',
            'icon' => 'el-icon-user-solid',
            'url' => $this->urlA('users'),
            'vars' => ['id'],
        ];
        $table->setOps('users', $configUsers);
        $table->setConfigs('opsWidth', 180);
    }


    /**
     * @param \suframe\form\Form $form
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     */
    private function getFormSetting($form)
    {
        $form->setRuleByClass(AdminAppsForm::class);
    }

    /**
     * 检测新app
     * @return \think\response\Json
     */
    public function checkNewApp()
    {
        try {
            $rs = Admin::apps()->checkNewApp();
            return $rs ? json_success() : json_error();
        } catch (\Exception $e) {
            return json_error($e->getMessage());
        }
    }

    /**
     * 安装
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function install()
    {
        $app_name = $this->requireParam('app_name');
        $rs = Admin::apps()->install($app_name);
        return $rs ? json_success() : json_error();
    }

    /**
     * 卸载
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function remove()
    {
        $app_name = $this->requireParam('app_name');
        $rs = Admin::apps()->remove($app_name);
        return $rs ? json_success() : json_error();
    }

    /**
     * @param AdminApps $model
     * @throws \Exception
     */
    private function beforeDelete($model)
    {
        //删除
        if ($model->setting_class && class_exists($model->setting_class)) {
            $settingClass = new $model->setting_class;
            Db::startTrans();
            try {
                $settingClass->uninstall($model);
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw $e;
            }
        }
    }

}