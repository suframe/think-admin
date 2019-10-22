<?php

namespace suframe\thinkAdmin\ui\table;

class UserTable implements TableInterface
{
    public function header()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'real_name' => '姓名',
            'avatar' => ['头像', 'image'],
            'create_time' => '创建时间',
        ];
    }

    public function filters()
    {
        return [
            'username',
            'create_time' => 'dateRange',
            'real_name' => [
                1 => '老王',
                2 => '老钱',
            ]
        ];
    }

    public function ops()
    {
        return [
            'edit' => [
                'type' => 'link',
                'title' => '编辑',
                'icon' => 'icon',
                'url' => 'edit',
                'vars' => ['id'],
            ],
            'del' => [
                'type' => 'link',
                'title' => '删除',
                'icon' => 'icon',
                'url' => 'del',
                'vars' => ['id'],
                'confirm' => '是否删除管理员？',
            ]
        ];
    }

    public function sort()
    {
        return ['id', 'create_time'];
    }
}