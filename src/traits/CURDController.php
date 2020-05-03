<?php

namespace suframe\thinkAdmin\traits;

use suframe\form\Form;
use suframe\thinkAdmin\ui\UITable;
use think\facade\Db;
use think\facade\View;

/**
 * Trait CURDController
 * @property  \think\Request request
 * @mixin AdminController
 * @method setNav($title)
 * @package suframe\thinkAdmin\traits
 */
trait CURDController
{

    /**
     * @param $action
     * @throws \Exception
     */
    private function CURLAllowActions($action)
    {
        $actions = ['index', 'delete', 'update'];
        if (!in_array($action, $actions)) {
            throw new \Exception('action not found');
        }
    }

    private function curlInit(){}
    private function getFormSetting($form){}
    private function getTableSetting($table){}
    private function beforeIndexRender($table){}
    private function beforeUpdateRender($form){}

    /**
     * @param \think\Model $model
     */
    private function beforeDelete($model){}

    /**
     * @return mixed
     */
    private function getManageModel(){}

    private function ajaxSearch()
    {
        $rs = $this->parseSearchWhere($this->getManageModel());
        return json_return($rs);
    }

    private function beforeSave($info, $post)
    {
        return $post;
    }

    private function getThinkAdminViewLayout()
    {
        $layout = thinkAdminPath() . 'view' . DIRECTORY_SEPARATOR . 'layout_container.html';
        View::assign('thinkAdminViewLayoutFile', $layout);
    }

    /**
     * @return string|\think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        $this->curlInit();
        $this->CURLAllowActions('index');
        if ($this->request->isAjax()) {
            return $this->ajaxSearch();
        }
        $this->getThinkAdminViewLayout();
        $table = new UITable();
        $this->getTableSetting($table);
        if(isset($this->currentNav)){
            $this->setNav($this->currentNav);
        }
        View::assign('table', $table);
        $this->beforeIndexRender($table);
        return View::fetch(config('thinkAdmin.view.commonTable'));
    }

    /**
     * @return mixed
     */
    private function getUpdateInfo()
    {
        if ($id = $this->request->param('id')) {
            return $this->getManageModel()::find($id);
        }
        return [];
    }

    /**
     * @return string|\think\response\Json
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function update()
    {
        $this->curlInit();
        $this->CURLAllowActions('update');
        $info = $this->getUpdateInfo();
        if ($this->request->isAjax() && $this->request->post()) {
            $post = $this->request->post();
            if (!$info) {
                $class = $this->getManageModel();
                $info = new $class;
            }
            Db::startTrans();
            try {
                $post = $this->beforeSave($info, $post);
                $rs = $info->save($post);
                Db::commit();
                return $this->handleResponse($rs);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw $e;
            }
        }
        $this->getThinkAdminViewLayout();
        if(isset($this->currentNav)){
            $this->setNav($this->currentNav);
        }
        $form = (new Form)->createElm();
        $form->setData($info);
        $this->getFormSetting($form);
        $title = (isset($this->currentNavZh) ? $this->currentNavZh : '') . ($info ? '编辑' : '新增');
        View::assign('pageTitle', $title);
        View::assign('form', $form);
        $this->beforeUpdateRender($form);
        return View::fetch(config('thinkAdmin.view.commonForm'));
    }

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function delete()
    {
        $this->curlInit();
        $this->CURLAllowActions('delete');
        $id = $this->requirePostInt('id');
        $model = $this->getManageModel()::find($id);
        if (!$model) {
            throw new \Exception('model not exist');
        }
        Db::startTrans();
        try {
            $this->beforeDelete($model);
            $rs = $model->delete();
            Db::commit();
            return $this->handleResponse($rs, '删除成功', '删除失败');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }

}