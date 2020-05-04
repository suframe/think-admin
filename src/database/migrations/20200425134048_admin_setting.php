<?php

use think\facade\Db;
use think\migration\Migrator;
use think\migration\db\Column;

class AdminSetting extends Migrator
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

        Db::startTrans();
        try{
            //通用配置组表
            $table = $this->table(
                config('thinkAdmin.database.setting_group', 'admin_setting_group'),
                array(
                    'engine' => 'InnoDB',
                    'comment' => '配置分组',
                ));
            $table->addColumn('key', 'string', ['comment' => '分组key', 'length' => 64])
                ->addColumn('name', 'string', ['comment' => '分组名称', 'length' => 64])
                ->addColumn('app_name', 'string', ['comment' => '应用', 'length' => 32, 'default' => 'system'])
                ->addColumn('inx', 'integer', ['comment' => '显示排序', 'null' => true, 'default' => 100])
                ->addTimestamps()
                ->addIndex(['key'], ['unique' => true])
                ->addIndex(['name'], ['unique' => true])
                ->addIndex(['inx'])
                ->create();

            //通用配置表 升级
            $table = $this->table(config('thinkAdmin.database.setting'));
            $table->removeIndex(['group']);
            $table->update();

            $table->renameColumn('group', 'group_key')
                ->renameColumn('order', 'inx')
                ->addColumn('app_name', 'string', ['comment' => '应用', 'length' => 32, 'default' => 'system'])
                ->addColumn('type', 'string', ['comment' => '类型', 'length' => 32])
                ->addColumn('default_value', 'text', ['comment' => '配置项', 'length' => 32])
                ->addColumn('placeholder', 'string', ['comment' => '提示信息', 'length' => 255])
                ->addColumn('require', 'integer', ['comment' => '是否必选', 'length' => 1])
                ->changeColumn('value', 'text', ['comment' => '值', 'null' => true])
                ->changeColumn('values', 'text', ['comment' => '选项值', 'null' => true])
                ->addIndex(['app_name'])
                ->addIndex(['group_key'])
                ->update();
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            throw $e;
        }
    }
}
