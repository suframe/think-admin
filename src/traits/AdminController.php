<?php

namespace suframe\thinkAdmin\traits;

use think\facade\View;
use think\Model;
use think\Request;
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
            $page,
            $nums
        ];
    }

    /**
     * 获取管理员
     * @return \suframe\thinkAdmin\model\AdminUsers
     */
    protected function getAdminUser()
    {
        return Admin::user();
    }

    /**
     * 返回处理
     * @param $rs
     * @param string $message
     * @param array $data
     * @return \think\response\Json
     */
    protected function handleResponse($rs, $message = '', $data = [])
    {
        if ($rs) {
            return json_success($message ?: 'success', $data);
        }
        return json_error($message ?: 'error', $data);
    }

    protected $urlPre;

    protected function urlA(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        return url($this->urlPre . $url, $vars, $suffix, $domain)->build();
    }

    protected function setAdminNavs($navs, $active)
    {
        View::assign('adminNavs', $navs);
        View::assign('adminNavActive', $active);
    }

    /**
     * 默认查询条件
     * @param Model $model
     * @param array $whereType
     * @param array $tableFields
     * @return mixed
     */
    protected function parseSearchWhere($model, $whereType = [], $tableFields = [])
    {
        $defaultParams = ['pageSize', 'sort', 'sortType'];

        if (!$tableFields) {
            $tableFields = $model->getTableFields();
        }
        foreach ($defaultParams as $defaultParam) {
            $tableFields[] = $defaultParam;
        }
        $params = $this->request->param($tableFields);

        //分页
        $pageSize = $params['pageSize'] ?? 10;
        $pageSize = intval($pageSize);
        if ($pageSize < 1) {
            $pageSize = 10;
        }
        if ($pageSize > 1000) {
            $pageSize = 1000;
        }

        //排序
        if (isset($params['sort'])) {
            $order = $params['sortType'] ?? 'desc';
            $model->order($params['sort'], $order === 'asc' ? 'asc' : 'desc');
        }
        foreach ($defaultParams as $defaultParam) {
            if (isset($params[$defaultParam])) {
                unset($params[$defaultParam]);
            }
        }

        foreach ($params as $key => $param) {
            if (is_array($params)) {
                $type = 'in';
            } else {
                $type = 'eq';
            }
            foreach ($whereType as $k => $item) {
                if (isset($params[$k])) {
                    $type = $item;
                }
            }
            switch ($type) {
                case 'eq':
                    $model->where($key, $param);
                    break;
                case 'neq':
                    $model->where($key, '<>', $param);
                    break;
                case 'gt':
                    $model->where($key, '>', $param);
                    break;
                case 'gtn':
                    $model->where($key, '>=', $param);
                    break;
                case 'lt':
                    $model->where($key, '<', $param);
                    break;
                case 'ltn':
                    $model->where($key, '<=', $param);
                    break;
                case 'in':
                    $model->whereIn($key, $param);
                    break;
                case 'notIn':
                    $model->whereNotIn($key, $param);
                    break;
                case 'like':
                    $model->whereLike($key, "%{$param}%");
                    break;
                case 'notLike':
                    $model->whereNotLike($key, "%{$param}%");
                    break;
                case 'betweenTime':
                    $model->whereBetweenTime($key, $param[0], $param[1]);
                    break;
                case 'between':
                    $model->whereBetween($key, $param);
                    break;
            }
        }
        return $model->paginate($pageSize);
    }
}