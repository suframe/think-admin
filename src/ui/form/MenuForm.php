<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminMenu;

class MenuForm
{

    public function parent_id()
    {
        $options = AdminMenu::buildOptions(0, true);
        return [
            'type' => 'select',
            'title' => '父菜单',
            'field' => 'parent_id',
            'options' => $options,
        ];
    }

    public function title()
    {
        return [
            'type' => 'input',
            'title' => '菜单名称',
            'field' => 'title',
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function uri()
    {
        return [
            'type' => 'input',
            'title' => '路由',
            'field' => 'uri',
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function order()
    {
        return [
            'type' => 'number',
            'title' => '排序',
            'field' => 'order',
            'value' => 100,
        ];
    }

    public function icon()
    {
        return [
            'type' => 'input',
            'title' => '图标',
            'field' => 'icon',
        ];
    }

}