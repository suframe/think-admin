<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminPermissions;

class LogsTable extends TableInterface
{
    public function header()
    {

        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'create_time' => '时间',
            'user_id' => '管理员ID',
            'ip' => 'IP',
            'path' => '请求path',
            'method' => ['label' => '请求method', 'width' => 120, 'filter' => AdminPermissions::$methods],
            'input' => '参数',
        ];
    }

    public function filters()
    {
        return [
            'ip' => ['label' => 'IP', 'type' => 'text'],
            'user_id' => ['label' => '管理员ID', 'type' => 'text'],
            'path' => ['label' => '请求path', 'type' => 'text'],
            'create_time' => ['label' => '请求path', 'type' => 'datetimerange'],
        ];
    }

}