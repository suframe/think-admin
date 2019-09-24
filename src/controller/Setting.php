<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use think\facade\Cache;

class Setting extends Base
{

    /**
     * @return int
     * @throws \Exception
     */
    public function group()
    {
        $group = $this->requireParam('group');
        return admin_config()->getGroup($group);
    }

    /**
     * @throws \Exception
     */
    public function get()
    {
        $key = $this->requireParam('key');
        return admin_config()->getKey($key);
    }

    /**
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function update()
    {
        $key = $this->requireParam('key');
        $value = $this->requireParam('value');
        return admin_config()->save($key, $value);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $key = $this->requireParam('key');
        return admin_config()->delete($key);
    }

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