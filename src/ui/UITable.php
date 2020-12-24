<?php

namespace suframe\thinkAdmin\ui;

use suframe\thinkAdmin\ui\table\TableInterface;

class UITable
{
    protected $id = 'app';
    protected $searchFormId = 'thinkSearchForm';

    /**
     * @param string $searchFormId
     */
    public function setSearchFormId(string $searchFormId): void
    {
        $this->searchFormId = $searchFormId;
    }

    protected $apiUrl;

    /**
     * @param string $id
     */
    public function setId(string $id): UITable
    {
        $this->id = $id;
        return $this;
    }

    public function createByClass($class)
    {
        /** @var TableInterface $obj */
        $obj = new $class;
        if ($header = $obj->header()) {
            $this->setHeader($header);
        }
        if ($filters = $obj->filters()) {
            $this->setFilter($filters);
        }
        if ($ops = $obj->ops()) {
            $this->setOps($ops);
        }
        return $this;
    }

    protected $configs = [];

    public function setConfigs($key, $value = null)
    {
        if (is_array($key)) {
            $this->configs = $key;
        } else {
            $this->configs[$key] = $value;
        }
        return $this;
    }

    protected $header = [];

    public function setHeader($key, $value = null)
    {
        if (is_array($key)) {
            $this->header = $this->setColumnFormHeader($key);
        } else {
            if(is_array($value)){
                $this->setColumn($key, $value);
                $value = $value['label'];
            }
            $this->header[$key] = $value;
        }
        return $this;
    }

    protected $column = [];

    protected function setColumnFormHeader($header){
        foreach ($header as $key => $item) {
            if (is_array($item)) {
                $header[$key] = $item['label'];
                $this->setColumn($key, $item);//设置显示类型
            }
        }
        return $header;
    }

    public function setColumn($key, $value)
    {
        if(isset($this->column[$key])){
            $this->column[$key] += $value;
        } else {
            $this->column[$key] = $value;
        }

        return $this;
    }

    protected $ops = [];

    public function setOps($key, $value = null)
    {
        if (is_array($key)) {
            $this->ops = $key;
        } else {
            $this->ops[$key] = $value;
        }
        return $this;
    }

    protected $breadcrumb = [];
    public function setBreadcrumb($title, $link = null)
    {
        if (is_array($title)) {
            $this->breadcrumb = $title;
        } else {
            $this->breadcrumb[] = $link ? [$title, $link] : $title;
        }
        return $this;
    }


    public function setEditOps($url, $vars, $config = []){
        $default = [
            'type' => 'link',
            'label' => '编辑',
            'icon' => 'el-icon-edit',
            'vars' => ['id'],
        ];
        $config = $config + $default;
        $config['url'] = $url;
        $config['vars'] = $vars;
        $this->setOps('edit', $config);
        return $this;
    }

    public function setDeleteOps($url, $vars, $config = []){
        $default = [
            'type' => 'ajax',
            'label' => '删除',
            'icon' => 'el-icon-delete',
            'confirm' => '确认删除？',
            'noReload' => false,
        ];
        $config = $config + $default;
        $config['url'] = $url;
        $config['vars'] = $vars;
        $this->setOps('delete', $config);
        return $this;
    }

    protected $filter = [];

    public function setFilter($key, $value = null)
    {
        if (is_array($key)) {
            $this->filter = $key;
        } else {
            $this->filter[$key] = $value;
        }
        return $this;
    }

    protected $html;

    /**
     * @return mixed
     */
    public function getHtml()
    {
        if (!$this->html) {
            $this->loadTemplate();
        }
        return $this->html;
    }

    protected $js;

    /**
     * @return mixed
     */
    public function getJs()
    {
        if (!$this->js) {
            $this->loadTemplate();
        }
        return $this->js;
    }

    protected $buttons = [];

    /**
     * @param $key
     * @param null $value
     * @return UITable
     */
    public function setButtons($key, $value = null)
    {
        if (is_array($key)) {
            $this->buttons = $key;
        } else {
            $this->buttons[$key] = $value;
        }
        return $this;
    }

    protected function loadTemplate()
    {
        ob_start();
        $id = $this->id;
        $header = $this->header;
        $column = $this->column;
        $ops = $this->ops;
        $filter = $this->filter;
        $apiUrl = $this->getApiUrl();
        $searchFormId = $this->searchFormId;
        $buttons = $this->buttons;
        $configs = $this->configs;
        $breadcrumb = $this->breadcrumb;
        require(__DIR__ . '/UITableTemplate.php');
        $rs = ob_get_clean();
        $rs = explode('<!-- split -->', $rs);
        $this->html = $rs[0];
        $this->js = $rs[1];
        return $this;
    }

    public function render()
    {
        return $this->html . $this->js;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

}