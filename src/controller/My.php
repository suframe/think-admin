<?php

namespace suframe\thinkAdmin\controller;

use suframe\form\Form;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\ui\form\AdminUserForm;
use think\facade\View;

class My extends Base
{

    protected $urlPre = '/thinkadmin/my/';

    protected function setNav($active)
    {
        $navs = [
            'index' => ['基本信息', $this->urlA('index')],
            'password' => ['修改密码', $this->urlA('password')],
        ];
        $this->setAdminNavs($navs, $active);
    }

    /**
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function index()
    {
        $this->setNav('index');
        $admin = $this->getAdminUser();
        $fields = [
            'real_name',
            'avatar',
        ];
        if ($this->request->isAjax() && $this->request->post()) {
            $post = $this->request->post();
            $rs = $admin->allowField($fields)->save($post);
            return $this->handleResponse($rs);
        }
        $admin = $this->getAdminUser();
        $form = (new Form)->createElm();
        $form->setData($admin->toArray());
        $form->setRuleByClass(AdminUserForm::class, [], $fields);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('my/base');
    }

    /**
     * 修改密码
     * @return string|\think\response\Json
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function password()
    {
        $this->setNav('password');
        if ($this->request->isAjax() && $this->request->post()) {
            $password = $this->request->post('password');
            $password_confirm = $this->request->post('password_confirm');
            if(!$password){
                return $this->handleResponse(false, '请输入密码');
            }
            if($password != $password_confirm){
                return $this->handleResponse(false, '密码不一致');
            }
            $auth = Admin::auth();
            $score = $auth->judgePassword($password);
            if($score < config('thinkAdmin.auth.judgePassword', 2)){
                return $this->handleResponse(false, '密码不安全');
            }
            $admin = $this->getAdminUser();
            $admin->password = Admin::auth()->hashPassword($password);
            return $this->handleResponse($admin->save());
        }
        $form = (new Form)->createElm();
        $form->setRuleByClass(AdminUserForm::class, [], ['password', 'password_confirm']);
        $formScript = $form->formScript();
        View::assign('formScript', $formScript);
        return View::fetch('my/base');
    }

}