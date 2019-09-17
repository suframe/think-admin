<?php

use think\facade\Env;

return [
    'enable' => Env::get('thinkAdmin.enable', 'true')
];