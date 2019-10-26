<?php

namespace suframe\thinkAdmin\ui\table;


abstract class TableInterface
{
    /**
     * 字段
     * @return mixed
     */
    public function header()
    {
        return [];
    }

    /**
     * 筛选
     * @return mixed
     */
    public function filters()
    {
        return [];
    }

    /**
     * 操作
     * @return mixed
     */
    public function ops()
    {
        return [];
    }

}