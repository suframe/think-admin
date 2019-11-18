<?php

namespace suframe\thinkAdmin\ui\table;

class AppsTable extends TableInterface
{
    public function header()
    {
        return [
            'id' => ['label' => 'ID', 'sort' => true, 'fixed' => 'left', 'width' => 80],
            'icon' => ['label' => '应用图标', 'type' => 'image'],
            'app_name' => '应用标识',
            'title' => '应用名称',
            'auth' => '开发者',
            'version' => '版本',
            'desc' => '应用描述',
            'installedName' => [
                'label' => '是否安装',
                'type' => 'link',
                'linkConfig'  => [
                    ['key' => 'installed', 'value' => 0, 'label' => '安装', 'icon' => 'fa fa-random', 'url' => '/thinkadmin/apps/install', 'vars' =>['app_name'], 'type' => 'ajax']
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