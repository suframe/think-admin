<?php

namespace suframe\thinkAdmin\controller;

use FormBuilder\Factory\Elm;
use suframe\form\Form;
use suframe\thinkAdmin\traits\SettingConfigController;
use think\facade\Cache;
use think\facade\View;

class System extends SystemBase
{
    use SettingConfigController;
    /**
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function index()
    {
        if ($this->request->isPost()) {
            return $this->doPost();
        }
        return $this->doShow();
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

    protected function getSettingAppName()
    {
        return 'system';
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