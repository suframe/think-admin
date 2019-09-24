<?php

use think\facade\Env;

return [
    'enable' => Env::get('thinkAdmin.enable', 'true'),
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
    ],

    'configGroups' => [
        'system' => '系统配置',
        'other' => '其他配置'
    ],

    'check_route_permission' => false, //
    'cache_admin_permission' => false, //缓存用户权限提高速度, 修改了权限需要更新缓存

];