<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ThinkAdminApps extends Migrator
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
            ->addTimestamps()
            ->addIndex(['app_name'], ['unique' => true])
            ->addIndex(['order'])
            ->create();
    }
}
