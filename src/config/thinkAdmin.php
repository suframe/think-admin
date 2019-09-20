<?php

use think\facade\Env;

return [
    'title' => '管理',
    'enable' => Env::get('thinkAdmin.enable', 'true'),
    'routeMiddleware' => [
        'Auth' => \suframe\thinkAdmin\middleware\Auth::class,
        'Log' => \suframe\thinkAdmin\middleware\Log::class,
        'Permission' => \suframe\thinkAdmin\middleware\Permission::class,
        'Boot' => \suframe\thinkAdmin\middleware\Boot::class,
    ],
    
    'database' => [
        'admin.database.users_table' => 'admin_users',
        'admin.database.roles_table' => 'admin_roles',
        'admin.database.permissions_table' => 'admin_permissions',
        'admin.database.menu_table' => 'admin_menu',
        'admin.database.user_permissions_table' => 'admin_user_permissions',
        'admin.database.role_users_table' => 'admin_role_users',
        'admin.database.role_menu_table' => 'admin_role_menu',
        'admin.database.operation_log_table' => 'admin_operation_log',
        'admin.database.role_permissions_table' => 'admin_role_permissions',
    ],

    'check_route_permission' => false

];