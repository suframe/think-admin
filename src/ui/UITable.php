<?php

namespace suframe\thinkAdmin\ui;

use suframe\thinkAdmin\ui\table\TableInterface;

class UITable
{
    protected $id = 'app';

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
        if ($filters = $obj->filters()) {
            $this->setFilter($filters);
        }
        if ($ops = $obj->ops()) {
            $this->setOps($ops);
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

    protected function loadTemplate()
    {
        ob_start();
        $id = $this->id;
        $header = $this->header;
        $column = $this->column;
        $ops = $this->ops;
        $filter = $this->filter;
        $apiUrl = $this->getApiUrl();
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