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
    public function change()
    {
        // create the table
        $table = $this->table(
            config('admin.database.users_table'),
            array(
                'engine' => 'InnoDB',
                'comment' => '用户表',
            ));
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('username', 'string', ['comment' => '用户名', 'length' => 190])
            ->addColumn('password', 'string', ['comment' => '密码', 'length' => 60])
            ->addColumn('name', 'string', ['comment' => '姓名', 'null' => true])
            ->addColumn('avatar', 'string', ['comment' => '头像', 'null' => true])
            ->addColumn('remember_token', 'string', ['comment' => '记住密码token', 'null' => true, 'length' => 60])
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['remember_token'])
            ->create();

        $admin = [
            'id' => 1,
            'username' => 'admin',
            'password' => 'admin',
            'name' => '超级管理员',
        ];
        $table->insert($admin)->save();
    }
}
