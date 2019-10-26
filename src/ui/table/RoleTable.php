<?php

namespace suframe\thinkAdmin\ui\table;

class RoleTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'name' => '名称',
            'slug' => '标识(英文字符)',
            'create_time' => ['label' => '创建时间', 'sort' => true],
        ];
    }

    public function filters()
    {
        return [
            'name' => ['label' => '用户名', 'type' => 'text']
        ];
    }

}