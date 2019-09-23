<?php
namespace suframe\thinkAdmin\traits;

use app\Request;

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
        if(!$value){
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
        if(!$value){
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
        if(!$value){
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
        if(!$value){
            throw new \Exception('integer needed');
        }
        return $value;
    }

}