<?php

namespace suframe\thinkAdmin\traits;

use app\Request;
use suframe\thinkAdmin\Admin;

/**
 * Trait AdminController
 * @package suframe\thinkAdmin\traits
 * @property Request $request
 */
trait AdminController
{

    /**
     * 获取post
     * @param $key
     * @param string $message
     * @param null $filter
     * @return mixed
     * @throws \Exception
     */
    protected function requirePost($key, $message = null, $filter = null)
    {
        $value = $this->request->post($key, null, $filter);
        if (!$value) {
            throw new \Exception($message ?: $message . ':' . $key);
        }
        return $value;
    }

    /**
     * @param $key
     * @param null $message
     * @throws \Exception
     */
    protected function requirePostInt($key, $message = null)
    {
        $value = $this->requirePost($key, $message, 'intval');
        if (!$value) {
            throw new \Exception('integer needed');
        }
        return $value;
    }

    /**
     * 获取get
     * @param $key
     * @param string $message
     * @param null $filter
     * @return mixed
     * @throws \Exception
     */
    protected function requireParam($key, $message = null, $filter = null)
    {
        $value = $this->request->param($key, null, $filter);
        if (!$value) {
            throw new \Exception($message ?: $message . ':' . $key);
        }
        return $value;
    }


    /**
     * @param $key
     * @param null $message
     * @throws \Exception
     */
    protected function requireParamInt($key, $message = null)
    {
        $value = $this->requireParam($key, $message, 'intval');
        if (!$value) {
            throw new \Exception('integer needed');
        }
        return $value;
    }

    /**
     * 分页参数
     * @param $numsDefault
     * @param int $numsMax
     * @param int $pageDefault
     * @return array
     * @throws \Exception
     */
    protected function requestPage($numsDefault = 20, $numsMax = 50, $pageDefault = 1)
    {
        $page = $this->request->param('page', $pageDefault);
        $nums = $this->request->param('nums', $numsDefault);
        if ($nums <= 0) {
            throw new \Exception('nums error');
        }
        $nums = $nums > $numsMax ? $numsMax : $nums;
        return [
            $page, $nums
        ];
    }

    /**
     * 获取管理员
     * @return \suframe\thinkAdmin\model\AdminUsers
     */
    protected function getAdminUser(){
        return Admin::user();
    }

}