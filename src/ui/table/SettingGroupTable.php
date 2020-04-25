<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\ui\table\TableInterface;

/**
 * 商城设置分组表格
 */
class SettingGroupTable extends TableInterface
{
    public function header()
    {
        return [
            'app_name'=> ['label' => '应用', 'filter' => AdminApps::buildAppsKeyValue(), 'field' => 'app_name'],
            'key'=> ['label' => '分组key'],
            'name'=> ['label' => '分组名称'],
            'inx'=> ['label' => '排序'],
            'create_time'=> ['label' => '创建时间'],
        ];
    }

    public function filters()
    {
        return [
            'key' => ['label' => '分组key', 'type' => 'text'],
            'name' => ['label' => '分组名称', 'type' => 'text'],
        ];
    }
}