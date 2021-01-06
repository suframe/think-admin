<?php

use think\migration\Migrator;

class AdminSettingValues extends Migrator
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
        //通用配置表 升级
        $table = $this->table(config('thinkAdmin.database.setting'));
        $table->addColumn('values', 'text', ['comment' => '多选选项', 'null' => true]);
        $table->update();
    }
}
