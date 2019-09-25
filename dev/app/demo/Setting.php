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
            'icon' => '',
            'auth' => 'suframe',
            'version' => '1.0',
            'desc' => 'demo app',
            'entry' => url('/demo/index/index')->build()
        ];
    }

    public function remove()
    {

    }

}
