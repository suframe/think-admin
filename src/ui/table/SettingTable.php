<?php

namespace suframe\thinkAdmin\ui\table;

class SettingTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'order' => ['label' => '排序', 'sort' => true, 'width' => 80],
            'group' => '分组名称',
            'name' => '配置名称',
            'key' => '配置key',
            'value' => '值',
        ];
    }

    public function filters()
    {
        return [
            'name' => ['label' => '用户名', 'type' => 'text'],
            'key' => ['label' => '配置key', 'type' => 'text'],
        ];
    }

}