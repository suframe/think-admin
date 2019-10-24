<?php

namespace suframe\thinkAdmin\ui\table;

class UserTable implements TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'username' => ['label' => '用户名', 'filter' => [1 => 'john', 2 => 'jack']],
            'real_name' => ['label' => '姓名', 'filter' => [1 => '老钱', 2 => '老王'], 'multiple' => true],
            'avatar' => ['label' => '头像', 'type' => 'image'],
            'create_time' => ['label' => '创建时间', 'sort' => true],
        ];
    }

    public function filters()
    {
        return [
            'id' => ['label' => 'ID', 'type' => 'text'],
            'create_time' => ['label' => '时间', 'type' => 'date'],
            'create_times' => ['label' => '时间范围', 'type' => 'daterange'],
            'avatar' => ['label' => '头像', 'type' => 'select', 'value' => ['类型1', '类型2']],
            'avatars' => ['label' => '头像多选', 'type' => 'select', 'value' => ['类型3', '类型4'], 'multiple' => true],
            'area' => [
                'label' => '地区',
                'type' => 'cascader',
                'multiple' => true,
                'checkStrictly' => true,
                'value' => [
                    [
                        'label' => '选项一',
                        'value' => '1',
                        'children' => [['label' => '选项一.1', 'value' => '1.1'], ['label' => '选项一.2', 'value' => '1.2']]
                    ],
                    [
                        'label' => '选项二',
                        'value' => '2',
                        'children' => [['label' => '选项二.1', 'value' => '2.1'], ['label' => '选项二.2', 'value' => '2.2']]
                    ],
                ]
            ]
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