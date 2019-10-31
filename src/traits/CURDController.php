<?php

namespace suframe\thinkAdmin\traits;

use suframe\form\Form;
use suframe\thinkAdmin\ui\UITable;
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

    protected $currentNav;
    protected $currentNavZh = '';
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

        $table = new UITable();
        $this->getTableSetting($table);
        if($this->currentNav){
            $this->setNav($this->currentNav);
        }
        View::assign('table', $table);
        $this->beforeIndexRender($table);
        return View::fetch('common/table');
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
            $post = $this->beforeSave($info, $post);
            $rs = $info->save($post);
            return $this->handleResponse($rs);
        }
        if($this->currentNav){
            $this->setNav($this->currentNav);
        }
        $form = (new Form)->createElm();
        $form->setData($info);
        $this->getFormSetting($form);
        $title = $this->currentNavZh . ($info ? '编辑' : '新增');
        View::assign('pageTitle', $title);
        View::assign('form', $form);
        $this->beforeUpdateRender($form);
        return View::fetch('common/form');
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
        $this->beforeDelete($model);
        return $this->handleResponse($model->delete(), '删除成功', '删除失败');
    }

}