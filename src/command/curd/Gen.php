<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\command\curd;

use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Db\Adapter\TablePrefixAdapter;
use think\console\Output;
use think\facade\Db;
use think\migration\db\Table;

/**
 * curd 生成助手
 * Class Gen
 * @package suframe\thinkAdmin\command\gen
 */
class Gen
{
    /**
     * @var Output
     */
    protected $output;

    /**
     * 设置输出
     * @param Output $output
     */
    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    protected $config = [];
    protected $layoutDir;

    /**
     * 配置
     * @param $config
     */
    public function setConfig($config): void
    {
        $this->config = $config;
    }

    /**
     * 生成
     * @param TablePrefixAdapter $adapter
     * @param string $tableName
     * @param $controller
     * @return bool
     */
    public function build(TablePrefixAdapter $adapter, string $tableName, $controller): bool
    {
        $default = config('database.default');
        $options = config("database.connections.{$default}");
        $sql = "
            SELECT TABLE_NAME, TABLE_COMMENT 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ";
        $rs = Db::query($sql, [$options['database'], $tableName]);
        if(!$rs || !isset($rs[0]) || !isset($rs[0]['TABLE_COMMENT'])){
            return false;
        }
        $table = array_pop($rs);
        preg_match('/\[([^\[\]]+)\]/', $table['TABLE_COMMENT'], $match);
        if(!$match){
            return false;
        }
        $comment = $match[1];
        echo '<pre>';
        print_r($comment);
        echo '<pre>';
        exit;


        $adapter = AdapterFactory::instance()->getAdapter($default, $options);
        $table = new Table($tableName, [], $adapter);
        echo '<pre>';
        print_r($table->getColumns());
        echo '<pre>';
        exit;

        if (!$table) {
            return false;
        }
        $sql = "desc `{$table}`";
        $rs = Db::query($sql);

        echo '<pre>';
        print_r($rs);
        echo '<pre>';
        exit;
        //读取数据库
        $sql = "SELECT column_name ,column_comment, data_type FROM INFORMATION_SCHEMA.Columns WHERE table_name='{$tableName}' AND table_schema='j_market'";


        $namespace = explode('/', $class);
        $className = array_pop($namespace);
        $namespace = implode('/', $namespace);
        $classDir = $this->appPath . $namespace . '/';

        //生成dao文件
        $classFile = $classDir . ucfirst($className) . 'Dao.php';
        //step1, 检查是否存在,防止冲突，已存在的不做处理
        if ($this->exist($classFile, $classDir, $namespace)) {
            $this->out($class . ' exist');
            return false;
        }
        $config = [
            'title' => $className,
            'table' => $tableName,
            'model' => $className,
        ];
        //step2, 替换layout相关参数，然后生成文件写入
        $this->buildClassFile('__dao', $config, $namespace, $className, $classFile);

        //model
        $classFile = $classDir . ucfirst($className) . '.php';
        //step1, 检查是否存在,防止冲突，已存在的不做处理
        if ($this->exist($classFile, $classDir, $namespace)) {
            $this->out($class . ' exist');
            return false;
        }

        //默认$className对应表名
        $table = table($tableName);
        $sql = "SELECT column_name ,column_comment, data_type FROM INFORMATION_SCHEMA.Columns WHERE table_name='{$tableName}' AND table_schema='j_market'";
        $columns = $table->execute($sql, \j\db\SqlFactory::SELECT)->toArray();
        $property = '';
        foreach ($columns as $column) {
            $type = $column['data_type'] == 'int' ? 'integer' : 'string';
            $property .= " * @property {$type} {$column['column_name']} {$column['column_comment']}\n";
        }
        $config = [
            'title' => $className,
            'property' => $property,
        ];
        //step2, 替换layout相关参数，然后生成文件写入
        $this->buildClassFile('__model', $config, $namespace, $className, $classFile);
    }

    protected function exist($classFile, $classDir, $namespace)
    {
        if (file_exists($classFile)) {
            return true;
        }

        if (!is_dir($classDir)) {
            //创建目录
            mkdir($classDir, 0777, true);
        }
        //创建Base.php 文件
        return false;
    }

    protected function buildClassFile($layout, $config, $namespace, $class, $file)
    {
        if (file_exists($file)) {
            return false;
        }
        $base = file_get_contents($this->layoutDir . $layout);

        //替换参数
        $base = str_replace('[layout:time]', date('Y-m-d H:i:s'), $base);
        if ($namespace) {
            $namespace = '\\' . $namespace;
        }
        $namespace = str_replace('/', '\\', $namespace);
        $base = str_replace('[layout:namespace]', $namespace, $base);
        $base = str_replace('[layout:title]', $config['title'], $base);
        $class = ucfirst($class);
        $base = str_replace('[layout:class]', $class, $base);
        if (isset($config['property'])) {
            $base = str_replace('[layout:property]', $config['property'], $base);
        }
        if (isset($config['table'])) {
            $base = str_replace('[layout:table]', $config['table'], $base);
        }
        if (isset($config['model'])) {
            $base = str_replace('[layout:model]', $config['model'], $base);
        }
        file_put_contents($file, $base);
        $this->out($file . ' ok');
        return true;
    }

    protected function out($message = null)
    {
        echo "{$message}\n";
    }
}
