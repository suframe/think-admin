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
    protected $allowTypes = [
        'image', 'images', 'file', 'files', 'switch', 'slider', 'sliderRange',
        'colorPicker', 'rate', 'checkbox', 'cascader', 'city', 'cityArea',
    ];
    protected $layoutDir;

    protected function getLayoutDir(): string
    {
        return config('thinkAdmin.view.genLayoutDir', __DIR__ . DIRECTORY_SEPARATOR . 'layout' . DIRECTORY_SEPARATOR);
    }

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
        $sql = "SELECT TABLE_NAME, TABLE_COMMENT" . " 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ";
        $rs = Db::query($sql, [$options['database'], $tableName]);
        if (!$rs || !isset($rs[0]) || !isset($rs[0]['TABLE_COMMENT'])) {
            return false;
        }
        $table = array_pop($rs);
        preg_match('/\[([^\[\]]+)\]/', $table['TABLE_COMMENT'], $match);
        if (!$match) {
            return false;
        }
        $tableComment = $match[1];

        $sqlFields = "SELECT COLUMN_NAME , COLUMN_COMMENT, DATA_TYPE, COLUMN_KEY, IS_NULLABLE" . "
                    FROM INFORMATION_SCHEMA.Columns 
                    WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
        $fields = Db::query($sqlFields, [$options['database'], $tableName]);
        if (!$fields) {
            return false;
        }

        $fieldsConfig = [];
        foreach ($fields as $field) {
            preg_match('/\[([^\[\]]+)\]/', $field['COLUMN_COMMENT'], $match);
            if (!$match) {
                continue;
            }
            $comment = $match[1];
            $columnName = $field['COLUMN_NAME'];
            $type = $field['DATA_TYPE'];
            if (strpos($comment, '@')) {
                $tmp = explode('@', $comment);
                $type = $tmp[1];
                if (!in_array($type, $this->allowTypes)) {
                    continue;
                }
                $comment = $tmp[0];
            } elseif ($field['COLUMN_KEY'] === 'PRI') {
                $type = 'pri';
            }
            $fieldsConfig[$columnName] = [
                'type' => $type,
                'comment' => $comment,
                'require' => $field['IS_NULLABLE'] === 'NO',
            ];
        }
        if (!$fieldsConfig) {
            return false;
        }

        $className = explode('_', $tableName);
        array_walk($className, function (&$v, $k) {
            return $v = ucfirst($v);
        });
        $className = implode('', $className);

        $tableClass = $this->buildTable($tableComment, $className, $fieldsConfig);
        $formClass = $this->buildForm($tableComment, $className, $fieldsConfig);

        //生成控制器
        $controller_layer = config('route.controller_layer');
        $namespace = app()->getNamespace() . '/' . $controller_layer;
        $filePath = app()->getAppPath() . $controller_layer . DIRECTORY_SEPARATOR . $className . '.php';
        $config = [
            'namespace' => $namespace,
            'model' => $className,
            'comment' => $tableComment,
            'class' => $className,
            'urlPre' => '/' . lcfirst($className) . '/',
            'table' => $tableClass,
            'form' => $formClass,
        ];
        $this->buildClassFile('controller', $config, $filePath);
    }

    protected function buildTable(string $tableComment, string $className, array $params): string
    {
        $table = [];
        foreach ($params as $field => $item) {
            if (strpos($item['comment'], '_' === 0)) {
                continue;
            }
            switch ($item['type']) {
                case 'pri':
                    $table[$field] = ['label' => $item['comment'], 'sort' => true, 'fixed' => 'left', 'width' => 80];
                    break;
                case 'image':
                case 'images':
                    $table[$field] = ['label' => $item['comment'], 'type' => $item['type']];
                    break;
                default:
                    $table[$field] = $item['comment'];
            }
        }
        if (!$table) {
            return '';
        }
        //生产文件
        $namespace = app()->getNamespace() . 'ui/table/';
        $filePath = app()->getBasePath() . 'ui' . DIRECTORY_SEPARATOR . 'table' . DIRECTORY_SEPARATOR;
        $className = $className . 'Table';
        $config = [
            'namespace' => $namespace,
            'comment' => $tableComment,
            'class' => $className,
            'config' => var_export($table, true),
        ];

        $this->buildClassFile('table', $config, $filePath . $className . '.php');
        return $namespace . $className;
    }

    protected function buildForm(string $tableComment, string $className, array $params): string
    {
        $form = [];
        foreach ($params as $field => $item) {
            if (strpos($item['comment'], '^' === 0)) {
                continue;
            }
            $filedSetting = [];
            switch ($item['type']) {
                case 'pri':
                    break;
                case 'int':
                    $filedSetting = ['type' => 'number'];
                    break;
                case 'image':
                case 'images':
                case 'file':
                case 'files':
                    $filedSetting = ['type' => 'upload' . ucfirst($item['type'])];
                    break;
                case 'switch':
                    $filedSetting = [
                        'type' => 'switch',
                        'props' => [
                            'activeValue' => "1",
                            'inactiveValue' => "0",
                        ]
                    ];
                    break;

                case 'slider':
                case 'sliderRange':
                    $filedSetting = [
                        'type' => 'slider',
                        'props' => [
                            'min' => 0,
                            'max' => 100,
                        ]
                    ];
                    if ($item['type'] == 'sliderRange') {
                        $filedSetting['props']['range'] = true;
                    }
                    break;
                case 'rate':
                    $filedSetting = [
                        'type' => $item['type'],
                        'props' => [
                            'max' => 5,
                        ]
                    ];
                    break;
                case 'radio':
                case 'checkbox':
                    $filedSetting = [
                        'type' => $item['type'],
                        'options' => [
                            ['value' => 0, 'label' => '请选择']
                        ]
                    ];
                    break;
                case 'cascader':
                case 'city':
                case 'cityArea':
                    $filedSetting = ['type' => $item['type']];
                    break;
                case 'colorPicker':
                    $filedSetting = ['type' => 'ColorPicker'];
                    break;

                default:
                    $filedSetting = ['type' => 'input'];
            }
            if (!$filedSetting) {
                continue;
            }
            $filedSetting['title'] = $item['comment'];
            $filedSetting['field'] = $field;
            if ($item['require']) {
                $filedSetting['validate'] = [
                    ['required' => true, 'message' => '不能为空']
                ];
            }
            $form[$field] = $filedSetting;
        }
        if (!$form) {
            return '';
        }

        //生产文件
        $namespace = app()->getNamespace() . 'ui/form/';
        $filePath = app()->getBasePath() . 'ui' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR;
        $className = $className . 'Form';
        $config = [
            'namespace' => $namespace,
            'comment' => $tableComment,
            'class' => $className,
        ];

        $configStr = '';
        foreach ($form as $field => $item) {
            $tmp = var_export($item, true);
            $configStr .= <<<EOF

public function {$field}()
{
    return {$tmp};
}
EOF;
        }

        $config['config'] = $configStr;
        $this->buildClassFile('form', $config, $filePath . $className . '.php');
        return $namespace . $className;
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

    protected function buildClassFile(string $layout, array $configs, string $file): bool
    {
        if (file_exists($file)) {
            return false;
        }
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        $base = file_get_contents($this->getLayoutDir() . $layout);

        foreach ($configs as $key => $config) {
            $base = str_replace("[layout:{$key}]", $config, $base);
        }
        file_put_contents($file, $base);
        $this->out($file . ' ok');
        return true;
    }

    protected function out(string $message): void
    {
        $this->output->writeln($message);
    }
}
