<?php

namespace suframe\thinkAdmin\ui\table;

class AppsTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'image' => ['label' => '应用封面', 'type' => 'image'],
            'type_name' => '应用类型',
            'app_name' => '应用标识',
            'title' => '应用名称',
            'auth' => '开发者',
            'version' => '版本',
            'desc' => '应用描述',
            'installedName' => [
                'label' => '是否安装',
                'type' => 'link',
                'linkConfig'  => [
                    ['key' => 'installed', 'value' => 0, 'label' => '安装', 'icon' => 'fa fa-random', 'url' => '/thinkadmin/apps/install', 'vars' =>['app_name'], 'type' => 'ajax'],
                    ['key' => 'installed', 'value' => 1, 'label' => '卸载', 'icon' => 'fa fa-random', 'url' => '/thinkadmin/apps/remove', 'vars' =>['app_name'], 'type' => 'ajax']
                ]
            ],
        ];
    }

    public function filters()
    {
        return [
            'title' => ['label' => '应用名称', 'type' => 'text'],
        ];
    }

}