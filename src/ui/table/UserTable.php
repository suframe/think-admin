<?php

namespace suframe\thinkAdmin\ui\table;

class UserTable extends TableInterface
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
            'username' => ['label' => '用户名', 'type' => 'text'],
            'create_time' => ['label' => '创建时间', 'type' => 'daterange'],
        ];
    }

}