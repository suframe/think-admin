<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use think\facade\Cache;

class System extends Base
{

    /**
     * 清除缓存
     * @return bool
     */
    public function clearCache()
    {
        //要清除的缓存项目
        return Cache::clear();
    }

    /**
     * 删除缓存key
     * @return bool
     * @throws \Exception
     */
    public function deleteCache()
    {
        $key = $this->requirePost('key');
        return Cache::delete($key);
    }
}