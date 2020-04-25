<?php

namespace suframe\thinkAdmin\ui\table;

use suframe\thinkAdmin\model\AdminApps;

class SettingTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'inx' => ['label' => '排序', 'sort' => true, 'width' => 80],
            'app_name'=> ['label' => '应用', 'filter' => AdminApps::buildAppsKeyValue(), 'field' => 'app_name'],
            'group_key' => '分组名称',
            'name' => '配置名称',
            'key' => '配置key',
        ];
    }

    public function filters()
    {
        return [
            'group_key' => ['label' => '分组名称', 'type' => 'text'],
            'key' => ['label' => '配置key', 'type' => 'text'],
        ];
    }

}