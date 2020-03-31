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

    'theme' => 'new',
    'welcomeUrl' => url('/thinkadmin/main/welcome')->build(),
    'menus' => [
        [
            'title' => '系统设置',
            'url' => '',
            'child' => [
                ['title' => '基本信息', 'uri' => url('/thinkadmin/system/index')->build()],
                ['title' => '用户管理', 'uri' => url('/thinkadmin/user/index')->build()],
                ['title' => '角色管理', 'uri' => url('/thinkadmin/role/index')->build()],
                ['title' => '菜单管理', 'uri' => url('/thinkadmin/menu/index')->build()],
                ['title' => '权限管理', 'uri' => url('/thinkadmin/permission/index')->build()],
                ['title' => '系统日志', 'uri' => url('/thinkadmin/logs/index')->build()],
                ['title' => '系统配置', 'uri' => url('/thinkadmin/setting/index')->build()],
                ['title' => '应用管理', 'uri' => url('/thinkadmin/apps/index')->build()],
            ]
        ]
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
        'apps_user' => 'admin_apps_user',
    ],

    'auth' => [
        'tokenName' => 'token',//token名称
        'max_fail' => '10', //最大登录错误次数
        'passwordSalt' => 'thinkAdmin', //密码加密后缀
        'judgePassword' => 2, //密码强度，1-9
        'captcha' => true,
        'driver' => \suframe\thinkAdmin\auth\SessionDriver::class, //认证驱动
        //自定义密码加密
        //'passwordHashFunc' => function($password) {return $password},
        //白名单
        'excepts' => [
            'thinkadmin/auth/login',
            'thinkadmin/auth/logout',
            'captcha.html'
        ]
    ],

    'view' => [
        'genLayoutDir' => thinkAdminPath() . 'command' . DIRECTORY_SEPARATOR . 'curd' . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR,
        'commonTable' => thinkAdminPath() . 'view' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'table.html',
        'commonForm' => thinkAdminPath() . 'view' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'form.html',
    ],

    'configGroups' => [
        'system' => '系统配置',
        'other' => '其他配置'
    ],

    'captcha' => true,

    'upload_url' => url('/thinkadmin/main/upload'),

    'controllers' => ['apps', 'auth', 'logs', 'main', 'menu', 'setting', 'system', 'user', 'my', 'role', 'permission'],

    'check_route_permission' => true, //
    'cache_admin_permission' => false, //缓存用户权限提高速度, 修改了权限需要更新缓存

];