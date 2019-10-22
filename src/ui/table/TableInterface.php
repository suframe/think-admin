<?php

namespace suframe\thinkAdmin\ui\table;


interface TableInterface
{
    /**
     * 字段
     * @return mixed
     */
    public function header();

    /**
     * 筛选
     * @return mixed
     */
    public function filters();

    /**
     * 操作
     * @return mixed
     */
    public function ops();

    public function sort();
}