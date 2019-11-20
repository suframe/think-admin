<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminApps;

class MenuTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'title' => '菜单名称',
            'uri' => '路由',
            'parent_id' => '父id',
            'icon' => '菜单图标',
            'order' => ['label' => '排序', 'sort' => true, 'width' => 80],
            'app_name_zh' => ['label' => '应用', 'field' => 'app_name', 'width' => 120, 'filter' => AdminApps::getAllNames()],
            'has_child' => [
                'label' => '子菜单',
                'type' => 'link',
                'linkConfig'  => [
                    ['key' => 'has_child', 'value' => true, 'showValue' => false, 'label' => '管理子菜单', 'icon' => 'fa fa-level-down', 'url' => '/thinkadmin/menu/index', 'vars' =>['id@parent_id']],
                ]
            ],
        ];
    }

    public function filters()
    {
        return [
            'title' => ['label' => '菜单名称', 'type' => 'text'],
            'uri' => ['label' => '路由', 'type' => 'text'],
        ];
    }

}