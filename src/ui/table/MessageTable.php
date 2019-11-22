<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminMessage;

class MessageTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'type_zh' => ['label' => '类型',  'width' => 120, 'field' => 'type', 'filter' => AdminMessage::getTypes()],
            'content' => '消息内容',
            'linkurl' => '链接',
            'created_time' => '时间',
        ];
    }
}