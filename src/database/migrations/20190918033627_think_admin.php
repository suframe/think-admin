<?php

use think\migration\Migrator;

class ThinkAdmin extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        //管理员
        $table = $this->table(
            config('thinkAdmin.database.users_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '用户表',
            ));

        $table->addColumn('username', 'string', ['comment' => '用户名', 'length' => 190])
            ->addColumn('password', 'string', ['comment' => '密码', 'length' => 60])
            ->addColumn('real_name', 'string', ['comment' => '姓名', 'null' => true])
            ->addColumn('avatar', 'string', ['comment' => '头像', 'null' => true])
            ->addColumn('access_token', 'string', ['comment' => '访问token', 'null' => true, 'length' => 32])
            ->addColumn('remember_token', 'string', ['comment' => '记住密码token', 'null' => true, 'length' => 60])
            ->addColumn('login_fail', 'integer', ['comment' => '登录失败次数', 'null' => true, 'default' => 0])
            ->addColumn('supper', 'integer', ['comment' => '是否超级管理员：1是', 'null' => true, 'default' => 0, 'length' => 1])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['remember_token'])
            ->addIndex(['access_token'])
            ->addTimestamps()
            ->create();
        $admin = [
            'username' => 'admin',
            'password' => '8659ae75747cc60aee5df2a651db7463',
            'real_name' => '超级管理员',
            'supper' => 1,
        ];
        $table->insert($admin)->save();

        //角色
        $table = $this->table(
            config('thinkAdmin.database.roles_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员角色表',
            ));
        $table->addColumn('name', 'string', ['comment' => '用户名', 'length' => 50])
            ->addColumn('slug', 'string', ['comment' => '标识', 'length' => 50])
            ->addTimestamps()
            ->addIndex(['name', 'slug'], ['unique' => true])
            ->create();
        $admin = [
            'id' => 1,
            'name' => '超级管理员',
            'slug' => 'admin',
        ];
        $table->insert($admin)->save();

        //管理员权限
        $table = $this->table(
            config('thinkAdmin.database.permissions_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员权限表',
            ));
        $table->addColumn('name', 'string', ['comment' => '权限名称', 'length' => 50])
            ->addColumn('slug', 'string', ['comment' => '标识', 'length' => 50])
            ->addColumn('http_method', 'string', ['comment' => '请求method', 'null' => true])
            ->addColumn('http_path', 'string', ['comment' => '请求path', 'null' => true])
            ->addTimestamps()
            ->addIndex(['name'], ['unique' => true])
            ->addIndex(['slug'], ['unique' => true])
            ->create();

        //管理菜单
        $table = $this->table(
            config('thinkAdmin.database.menu_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理菜单',
            ));
        $table->addColumn('parent_id', 'integer', ['comment' => '父id', 'default' => 0])
            ->addColumn('order', 'integer', ['comment' => '排序', 'default' => 100])
            ->addColumn('title', 'string', ['comment' => '菜单名称', 'length' => 50])
            ->addColumn('icon', 'string', ['comment' => '菜单图标', 'length' => 50, 'null' => true])
            ->addColumn('uri', 'string', ['comment' => '路由', 'length' => 50])
            ->addColumn('permission', 'string', ['comment' => '权限', 'null' => true])
            ->addIndex(['order'])
            ->create();

        //管理员角色表
        $table = $this->table(
            config('thinkAdmin.database.role_users_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员角色表',
            ));
        $table->addColumn('role_id', 'integer', ['comment' => '角色ID'])
            ->addColumn('user_id', 'integer', ['comment' => '用户ID'])
            ->addIndex(['role_id'])
            ->addIndex(['user_id'])
            ->create();

        //管理员角色权限表
        $table = $this->table(
            config('thinkAdmin.database.role_permissions_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员角色权限表',
            ));
        $table->addColumn('role_id', 'integer', ['comment' => '角色ID'])
            ->addColumn('permission_id', 'integer', ['comment' => '权限ID'])
            ->addIndex(['role_id'])
            ->addIndex(['permission_id'])
            ->create();

        //管理员权限表
        $table = $this->table(
            config('thinkAdmin.database.user_permissions_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员权限表',
            ));
        $table->addColumn('user_id', 'integer', ['comment' => '管理员ID'])
            ->addColumn('permission_id', 'integer', ['comment' => '权限ID'])
            ->addIndex(['user_id'])
            ->addIndex(['permission_id'])
            ->create();

        //管理员角色菜单表
        $table = $this->table(
            config('thinkAdmin.database.role_menu_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员角色菜单表',
            ));
        $table->addColumn('role_id', 'integer', ['comment' => '角色ID'])
            ->addColumn('menu_id', 'integer', ['comment' => '菜单ID'])
            ->addIndex(['role_id'])
            ->addIndex(['menu_id'])
            ->create();

        //管理员日志表
        $table = $this->table(
            config('thinkAdmin.database.operation_log_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '管理员日志表',
            ));
        $table->addColumn('user_id', 'integer', ['comment' => '管理员ID'])
            ->addColumn('path', 'string', ['comment' => '路径'])
            ->addColumn('method', 'string', ['comment' => '方法'])
            ->addColumn('ip', 'string', ['comment' => 'IP'])
            ->addColumn('input', 'string', ['comment' => '参数'])
            ->addTimestamps()
            ->addIndex(['user_id'])
            ->create();

        //通用配置表
        $table = $this->table(
            config('thinkAdmin.database.setting'),
            array(
                'engine' => 'InnoDB',
                'comment' => '通用配置表',
            ));
        $table->addColumn('group', 'string', ['comment' => '分组名称', 'length' => 64])
            ->addColumn('key', 'string', ['comment' => '配置key', 'length' => 64])
            ->addColumn('name', 'string', ['comment' => '配置名称', 'length' => 64])
            ->addColumn('value', 'text', ['comment' => '值'])
            ->addColumn('order', 'integer', ['comment' => '显示排序', 'null' => true, 'default' => 100])
            ->addIndex(['group'], ['unique' => true])
            ->addIndex(['key'], ['unique' => true])
            ->addIndex(['name'], ['unique' => true])
            ->addIndex(['order'])
            ->create();

        //apps表
        $table = $this->table(
            config('thinkAdmin.database.apps'),
            array(
                'engine' => 'InnoDB',
                'comment' => '应用管理',
            ));
        $table->addColumn('app_name', 'string', ['comment' => '应用标识', 'length' => 64])
            ->addColumn('title', 'string', ['comment' => '应用标题', 'length' => 128])
            ->addColumn('icon', 'string', ['comment' => '应用图标', 'length' => 128])
            ->addColumn('auth', 'string', ['comment' => '开发者', 'length' => 128])
            ->addColumn('version', 'string', ['comment' => '版本', 'length' => 64])
            ->addColumn('desc', 'string', ['comment' => '应用描述', 'length' => 255])
            ->addColumn('entry', 'string', ['comment' => '应用入口', 'length' => 255])
            ->addColumn('order', 'integer', ['comment' => '显示排序', 'null' => true, 'default' => 100])
            ->addColumn('installed', 'integer', ['comment' => '是否已安装：1是', 'null' => true, 'default' => 0])
            ->addColumn('setting_class', 'string', ['comment' => '安装的class', 'length' => 255])
            ->addTimestamps()
            ->addIndex(['app_name'], ['unique' => true])
            ->addIndex(['order'])
            ->addIndex(['installed'])
            ->create();

    }

    /**
     *
     */
    public function down()
    {
        $this->table(config('thinkAdmin.database.users_table'))->drop();
        $this->table(config('thinkAdmin.database.roles_table'))->drop();
        $this->table(config('thinkAdmin.database.permissions_table'))->drop();
        $this->table(config('thinkAdmin.database.menu_table'))->drop();
        $this->table(config('thinkAdmin.database.user_permissions_table'))->drop();
        $this->table(config('thinkAdmin.database.role_users_table'))->drop();
        $this->table(config('thinkAdmin.database.role_menu_table'))->drop();
        $this->table(config('thinkAdmin.database.operation_log_table'))->drop();
        $this->table(config('thinkAdmin.database.role_permissions_table'))->drop();
        $this->table(config('thinkAdmin.database.setting'))->drop();
        $this->table(config('thinkAdmin.database.apps'))->drop();
    }
}
