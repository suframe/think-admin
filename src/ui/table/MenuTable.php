<?php

namespace suframe\thinkAdmin\ui\table;

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