<?php
declare (strict_types = 1);

namespace app\demo\controller;

use FormBuilder\Driver\CustomComponent;
use suframe\form\Form;
use think\facade\View;

/**
 * Class Index
 * @package app\demo\controller
 * @menu 测试
 * @permission *
 */
class Index
{
    /**
     * @menu 菜单
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \ReflectionException
     */
    public function index()
    {

        $form = (new Form)->createElm();
        $form->setData([
            'sku' => [
                'title' => 'sssss'
            ]
        ]);
        $form->setRuleByClass(\app\ui\form\NewsForm::class);
        View::assign('form', $form);
        $this->getThinkAdminViewLayout();
        return View::fetch(config('thinkAdmin.view.commonForm'));
    }

    private function getThinkAdminViewLayout()
    {
        $layout = thinkAdminPath() . 'view' . DIRECTORY_SEPARATOR . 'layout_container.html';
        View::assign('thinkAdminViewLayoutFile', $layout);
    }
}
