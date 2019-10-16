<?php
namespace suframe\thinkAdmin\controller;

use suframe\form\facade\Form;
use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\ui\form\AdminUserForm;
use think\facade\View;

/**
 * 后台通用设置
 * Class Setting
 * @package suframe\thinkAdmin\controller
 */
class Setting extends SystemBase
{

    public function index()
    {
        $this->setNav('setting');
        return View::fetch('setting/index');
    }

    /**
     * 获取分组
     * @return mixed
     */
    public function group()
    {
        return json_return(config('thinkAdmin.configGroups'));
    }

    /**
     * 获取分组下配置
     * @return array|\think\Collection
     * @throws \Exception
     */
    public function findByGroup()
    {
        $group = $this->requireParam('group');
        return Admin::setting()->getGroup($group);
    }

    /**
     * 通过key获取单个配置
     * @throws \Exception
     */
    public function get()
    {
        $key = $this->requireParam('key');
        return Admin::setting()->getKey($key);
    }

    /**
     * 更新
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
        return Admin::setting()->save($key, $value);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        $key = $this->requireParam('key');
        return Admin::setting()->delete($key);
    }

}