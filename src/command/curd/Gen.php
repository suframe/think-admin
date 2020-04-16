<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\command\curd;

use Phinx\Db\Adapter\TablePrefixAdapter;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminPermissions;
use think\console\Output;
use think\facade\Db;

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
        'image',
        'images',
        'file',
        'files',
        'switch',
        'slider',
        'sliderRange',
        'color',
        'rate',
        'radio',
        'checkbox',
        'cascader',
        'city',
        'cityArea',
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

    protected $app;

    public function setApp(string $app): void
    {
        $this->app = $app;
    }

    protected $force;

    public function setForce(bool $force): void
    {
        $this->force = $force;
    }

    protected $menus;

    public function setMenu(bool $menu): void
    {
        $this->menus = $menu;
    }

    protected function getFileGenPath(): string
    {
        $path = app()->getAppPath();
        if ($this->app) {
            $path .= $this->app . DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    protected function getFileNamespace(): string
    {
        $namespace = app()->getNamespace();
        if ($this->app) {
            $namespace .= '\\' . $this->app;
        }
        return $namespace;
    }

    /**
     * 生成
     * @param TablePrefixAdapter $adapter
     * @param string $tableName
     * @param $controller
     * @return bool
     */
    public function build(TablePrefixAdapter $adapter, string $tableName, string $controller = ''): bool
    {
        $default = config('database.default');
        $options = config("database.connections.{$default}");
        if(($pos = strpos($tableName, '.')) !== false) {
            $options['database'] = substr($tableName, 0, $pos);
            $tableName = substr($tableName, $pos + 1, strlen($tableName));
        }
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
        if ($controller) {
            //自定义控制器文件，相对app/ 路径，例如 controller/goods/myGoods.php
            $filePath = $controller;
            if (strpos($controller, '.php') !== false) {
                $filePath .= '.php';
            }
            $namespace = str_replace('.php', '', $filePath);
            $namespace = explode('/', $namespace);
            $className = array_pop($namespace);
            $className = ucfirst($className);
            $namespace = implode('\\', $namespace);
        } else {
            $namespace = $controller_layer;
            $filePath = $controller_layer . DIRECTORY_SEPARATOR . $className . '.php';
        }

        $namespace = $this->getFileNamespace() . '\\' . $namespace;
        $filePath = $this->getFileGenPath() . $filePath;
        $urlPre = '/' . lcfirst($className) . '/';
        if ($this->app) {
            $urlPre = '/' . $this->app . $urlPre;
        }
        $config = [
            'namespace' => $namespace,
            'model' => $className,
            'comment' => $tableComment,
            'class' => $className,
            'urlPre' => $urlPre,
            'table' => $tableClass,
            'form' => $formClass,
        ];
        $rs = $this->buildClassFile('controller', $config, $filePath);
        if ($rs && $this->menus) {
            $this->addMenu($namespace, $className, $tableComment);
        }
        return $rs;
    }

    /**
     * 增加菜单和权限
     * @param string $namespace
     * @param string $className
     * @param string $title
     * @return bool
     */
    protected function addMenu(string $namespace, string $className, string $title): bool
    {
        $parentId = 0;
        if ($this->app) {
            //找出上级
            $parentId = AdminMenu::where('app_name', $this->app)
                ->where('parent_id', 0)->field('id')->value('id', 0);
        }
        $controller_layer = config('route.controller_layer');
        //指定控制器
        $uri = str_replace('\\', '/', $namespace);
        $uri = substr($uri, strlen(app()->getNamespace()), strlen($uri));
        $uri = str_replace("/{$controller_layer}/", '/', $uri . '/');
        $uri .= lcfirst($className) . '/index';
        if (AdminMenu::where('uri', $uri)->count()) {
            return false;
        }
        $menu = [
            'parent_id' => $parentId,
            'title' => $title,
            'icon' => $parentId == 0 ? 'el-icon-apple' : '',
            'uri' => $uri,
            'app_name' => $this->app,
        ];
        $menuModel = new AdminMenu();
        $rs = $menuModel->save($menu);

        if (!$rs) {
            return false;
        }
        //增加默认权限
        $httpPath = $uri . '/*';
        $info = [
            'name' => $title,
            'slug' => $httpPath,
            'http_method' => 'ALL',
            'http_path' => $httpPath,
            'app_name' => $this->app,
        ];
        $permissionsModel = new AdminPermissions();
        return $permissionsModel->save($info);
    }

    protected function buildTable(string $tableComment, string $className, array $params): string
    {
        $table = [];
        foreach ($params as $field => $item) {
            if (strpos($item['comment'], '_') === 0) {
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
                    $table[$field] = ['label' => $item['comment']];
            }
        }
        if (!$table) {
            return '';
        }
        //生产文件
        $namespace = $this->getFileNamespace() . '\ui\table';
        $filePath = $this->getFileGenPath() . 'ui' . DIRECTORY_SEPARATOR . 'table' . DIRECTORY_SEPARATOR;
        $className = $className . 'Table';
        $config = [
            'namespace' => $namespace,
            'comment' => $tableComment,
            'class' => $className,
            'config' => var_export($table, true),
        ];

        foreach ($table as $k => $v) {
            $config['filter_field'] = $k;
            $config['filter_label'] = $v['label'];
            break;
        }

        $this->buildClassFile('table', $config, $filePath . $className . '.php');
        return $namespace . '\\' . $className;
    }

    protected function buildForm(string $tableComment, string $className, array $params): string
    {
        $form = [];
        foreach ($params as $field => $item) {
            if (strpos($item['comment'], '^') === 0) {
                continue;
            }
            $filedSetting = [];
            switch ($item['type']) {
                case 'pri':
                    break;
                case 'int':
                    $filedSetting = ['type' => 'number'];
                    break;
                case 'datetime':
                    $filedSetting = ['type' => 'dateTime'];
                    break;
                case 'image':
                case 'images':
                case 'file':
                case 'files':
                    $filedSetting = ['type' => 'upload' . ucfirst($item['type']), 'action' => '__UPLOAD_ACTION__'];
                    break;
                case 'editor':
                    $filedSetting = ['type' => $item['type'], 'action' => '__UPLOAD_ACTION__'];
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
                case 'year':
                case 'month':
                case 'dateRange':
                case 'dates':
                    $filedSetting = ['type' => $item['type']];
                    break;
                case 'color':
                    $filedSetting = ['type' => 'color'];
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
        $namespace = $this->getFileNamespace() . '\ui\form';
        $filePath = $this->getFileGenPath() . 'ui' . DIRECTORY_SEPARATOR . 'form' . DIRECTORY_SEPARATOR;
        $className = $className . 'Form';
        $config = [
            'namespace' => $namespace,
            'comment' => $tableComment,
            'class' => $className,
        ];

        $configStr = '';
        foreach ($form as $field => $item) {
            $tmp = var_export($item, true);
            if (strpos($tmp, '__UPLOAD_ACTION__') !== false) {
                $tmp = str_replace("'__UPLOAD_ACTION__'", "config('thinkAdmin.upload_url')", $tmp);
            }
            $configStr .= <<<EOF
    public function {$field}()
    {
        return {$tmp};
    }
EOF;
        }
        $config['config'] = $configStr;
        $this->buildClassFile('form', $config, $filePath . $className . '.php');
        return $namespace . '\\' . $className;
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
        if (!$this->force && file_exists($file)) {
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