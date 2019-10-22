<?php

namespace suframe\thinkAdmin\controller;

use suframe\form\facade\Form;
use suframe\thinkAdmin\facade\Admin;
use suframe\thinkAdmin\model\AdminSetting;
use suframe\thinkAdmin\ui\form\SystemInfoForm;
use think\facade\Cache;
use think\facade\View;

class System extends SystemBase
{

    /**
     * 基本信息
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function index()
    {
        $group = 'system_info';
        if ($this->request->isAjax() && $this->request->post()) {
            $post = $this->request->post();
            $rs = Admin::setting()->saveByGroup($group, $post);
            return $this->handleResponse($rs);
        }

        $data = Admin::setting()->getGroupToArray($group);
        $this->setNav('system');
        $form = Form::createElm();
        $form->setData($data);
        $form->setRuleByClass(SystemInfoForm::class);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        View::assign('pageTitle', '基本信息');
        return View::fetch('common/form');
    }
    /**
     * 清除缓存
     * @return bool
     */
    public function clearCache()
    {
        //要清除的缓存项目
        return Cache::clear();
    }

    /**
     * 删除缓存key
     * @return bool
     * @throws \Exception
     */
    public function deleteCache()
    {
        $key = $this->requirePost('key');
        return Cache::delete($key);
    }
}