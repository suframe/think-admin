<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminPermissions;

class PermissionTable extends TableInterface
{
    public function header()
    {

        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'order' => ['label' => '排序', 'sort' => true, 'width' => 80],
            'name' => '权限名称',
            'slug' => '标识',
            'http_path' => '请求path',
            'http_method' => ['label' => '请求method', 'width' => 120, 'filter' => AdminPermissions::$methods],
        ];
    }

    public function filters()
    {
        return [
            'name' => ['label' => '权限名称', 'type' => 'text'],
            'slug' => ['label' => '标识', 'type' => 'text'],
            'http_path' => ['label' => '请求path', 'type' => 'text'],
        ];
    }

}