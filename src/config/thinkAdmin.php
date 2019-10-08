<?php

use think\facade\Env;

return [
    'enable' => Env::get('thinkAdmin.enable', 'true'),
    'uri_pre' => 'admin/thinkadmin/',
    'routeMiddleware' => [
        'Auth' => \suframe\thinkAdmin\middleware\Auth::class,
        'Log' => \suframe\thinkAdmin\middleware\Log::class,
        'Permission' => \suframe\thinkAdmin\middleware\Permission::class,
        'Boot' => \suframe\thinkAdmin\middleware\Boot::class,
    ],

    'database' => [
        'users_table' => 'admin_users',
        'roles_table' => 'admin_roles',
        'permissions_table' => 'admin_permissions',
        'menu_table' => 'admin_menu',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table' => 'admin_role_users',
        'role_menu_table' => 'admin_role_menu',
        'role_permissions_table' => 'admin_role_permissions',
        'operation_log_table' => 'admin_operation_log',
        'setting' => 'admin_setting',
        'apps' => 'admin_apps',
    ],

    'auth' => [
        'tokenName' => 'token',//token名称
        'max_fail' => '10', //最大登录错误次数
        'passwordSalt' => 'thinkAdmin', //密码加密后缀
        'driver' => \suframe\thinkAdmin\auth\SessionDriver::class, //认证驱动
        //自定义密码加密
        //'passwordHashFunc' => function($password) {return $password},
        //白名单
        'excepts' => [
            'thinkadmin/auth/login',
            'thinkadmin/auth/logout',
        ]
    ],

    'configGroups' => [
        'system' => '系统配置',
        'other' => '其他配置'
    ],

    'check_route_permission' => true, //
    'cache_admin_permission' => false, //缓存用户权限提高速度, 修改了权限需要更新缓存

];