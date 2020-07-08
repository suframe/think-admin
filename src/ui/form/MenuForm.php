<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminMenu;

class MenuForm
{

    public function parent_id()
    {
        $options = AdminMenu::buildOptions('all', true);
        return [
            'type' => 'select',
            'title' => '父菜单',
            'field' => 'parent_id',
            'options' => $options,
            'props' => [
                'filterable' => true,
            ],
        ];
    }

    public function app_name()
    {
        return [
            'type' => 'select',
            'options' => AdminApps::buildAppsOptions(),
            'title' => '应用',
            'field' => 'app_name',
            'validate' => [
                ['required' => true, 'message' => '必选']
            ]
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

    public function icon()
    {
        return [
            'type' => 'input',
            'title' => '图标',
            'field' => 'icon',
        ];
    }

    public function show_menu()
    {
        return [
            'type' => 'radio',
            'title' => '显示到菜单',
            'field' => 'show_menu',
            'options' => [
                ['value' => 1, 'label' => "是"],
                ['value' => 2, 'label' => "否"],
            ],
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

}