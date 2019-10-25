<?php

namespace suframe\thinkAdmin\ui\table;

class UserTable implements TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'username' => '用户名',
            'real_name' => '姓名',
            'avatar' => ['label' => '头像', 'type' => 'image'],
            'create_time' => ['label' => '创建时间', 'sort' => true],
        ];
    }

    public function filters()
    {
        return [
            'id' => ['label' => 'ID', 'type' => 'text'],
            'create_time' => ['label' => '创建时间', 'type' => 'daterange'],
        ];
    }

    public function ops()
    {
        return [
            'edit' => [
                'type' => 'link',
                'label' => '编辑',
                'icon' => 'el - icon - edit',
                'url' => '/thinkadmin/user/edit',
                'vars' => ['id'],
            ],
            'del' => [
                'type' => 'ajax',
                'label' => '删除',
                'icon' => 'el - icon - delete',
                'url' => '/thinkadmin/user/delete',
                'vars' => ['id'],
                'confirm' => '是否删除管理员？',
            ]
        ];
    }

}