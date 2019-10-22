<?php

namespace suframe\thinkAdmin;

use suframe\thinkAdmin\model\AdminSetting;
use suframe\thinkAdmin\traits\SingleInstance;
use think\Collection;

class Setting extends Collection
{
    use SingleInstance;

    /**
     * @param $key
     * @param string $default
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getKey($key, $default = '')
    {
        list($group, $key) = $this->formatKey($key);
        $setting = AdminSetting::where('key', $key);
        if ($group) {
            $setting->where('group', $group);
        }
        $setting->order('order', 'desc');
        return $setting->find() ?: $default;
    }

    /**
     * @param $group
     * @param array $default
     * @return array|Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroup($group, $default = [])
    {
        $setting = AdminSetting::where('group', $group);
        $setting->order('order', 'desc');
        return $setting->select() ?: $default;
    }

    /**
     * @param $group
     * @param array $default
     * @return array|Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupToArray($group, $default = [])
    {
        $rs = $this->getGroup($group, $default);
        if($rs && is_object($rs)){
            $rs = $rs->column('value', 'key');
        }
        return $rs;
    }

    public function saveByGroup($group, $post = [])
    {
        $rs = false;
        foreach ($post as $key => $item) {
            $setting = AdminSetting::where('group', $group)
                ->where('key', $key)->find();
            if($setting){
                $setting->value = $item;
            } else {
                $setting = new AdminSetting();
                $setting->group = $group;
                $setting->key = $key;
                $setting->name = $key;
                $setting->value = $item;
            }
            $rs = $setting->save() || $rs;
        }
        return $rs;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save($key, $value)
    {
        list($group, $key) = $this->formatKey($key);
        $setting = AdminSetting::where('key', $key);
        if ($group) {
            $setting->where('group', $group);
        }
        $info = $setting->find();
        if (!$info) {
            $info = new AdminSetting();
        }
        $info->key = $key;
        $info->group = $group;
        $info->value = $value;
        return $setting->save();
    }

    /**
     * 删除
     * @param $key
     * @return bool
     * @throws \Exception
     */
    public function delete($key)
    {
        list($group, $key) = $this->formatKey($key);
        if ($group && ($key === '*')) {
            return AdminSetting::where('group', $group)->delete();
        }
        $setting = AdminSetting::where('key', $key);
        if ($group) {
            $setting->where('group', $group);
        }
        return $setting->delete();
    }

    protected function formatKey($key)
    {
        $keys = explode('.', $key);
        $group = null;
        if (count($keys) > 1) {
            $group = array_shift($keys);
        }
        $key = implode('.', $keys);
        return [
            $group,
            $key
        ];
    }
}