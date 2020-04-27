<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminApps extends ModelBase
{
    const TYPE_LOCAL = 'local';
    const TYPE_REMOTE = 'remote';

    public static function getAllNames()
    {
        return AdminApps::field(['app_name', 'title'])
            ->where('installed', 1)
            ->column('title', 'app_name');
    }

    public static function buildAppsKeyValue($hasAll = false)
    {
        $rs = static::buildKeyValue('app_name', 'title', $hasAll);
        return array_merge(['system' => '系统'], $rs);
    }

    public static function buildAppsOptions($hasAll = false)
    {
        $rs = static::buildOptions('app_name', 'title', $hasAll);
        array_unshift($rs, ['value' => 'system', 'label' => '系统']);
        return $rs;
    }

    /**
     * 获取类型
     * @param null $key
     * @param null $def
     * @return array|mixed|null
     */
    public static function getTypes($key = null, $def = null)
    {
        $config = [
            static::TYPE_LOCAL => '本地',
            static::TYPE_REMOTE => '远程',
        ];
        if ($key === null) {
            return $config;
        }
        return $config[$key] ?? $def;
    }

    public static function getTypeOptions()
    {
        $types = static::getTypes();
        $options = [];
        foreach ($types as $key => $type) {
            $options[] = [
                'label' => $type,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getTypeNameAttr($value, $data)
    {
        return static::getTypes($data['type']);
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核'];
        return $status[0];
    }

    //
    public function getInstalledNameAttr()
    {
        return $this->isInstalled() ? '已安装' : '未安装';
    }

    public function isInstalled()
    {
        return $this->installed === 1;
    }

}