<?php

namespace app\demo;

use suframe\thinkAdmin\AppSettingInterface;

class Setting extends AppSettingInterface
{

    public function info()
    {
        return [
            'app_name' => 'demo',
            'title' => 'demo',
            'image' => 'https://s2.ax1x.com/2019/11/18/Mci8XV.png',
            'auth' => 'suframe',
            'version' => '1.0',
            'desc' => 'demo app',
            'entry' => url('/demo/index/index')->build(),
            'menu_title' => 'demo',
            'menu_icon' => 'el-icon-s-goods',
        ];
    }

    public function remove()
    {

    }

}
