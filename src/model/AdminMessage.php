<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin Model
 */
class AdminMessage extends Model
{

    const TYPE_SYSTEM = 'system';
    const TYPE_USER = 'user';
    const TYPE_APPS = 'apps';
    const TYPE_OTHER = 'other';

    /**
     * 类型
     * @param null $key
     * @param null $default
     * @return array|mixed|null
     */
    public static function getTypes($key = null, $default = null)
    {
        $config = [
            static::TYPE_SYSTEM => '系统',
            static::TYPE_USER => '用户',
            static::TYPE_APPS => '应用',
            static::TYPE_OTHER => '其他',
        ];
        if ($key === null) {
            return $config;
        }
        return $config[$key] ?? $default;
    }


    /**
     * 类型中文
     * @param $value
     * @param $data
     * @return array|mixed|null
     */
    public function getTypesAttr($value, $data)
    {
        return static::getTypes($data['type']);
    }


    /**
     * 类型中文
     * @param $value
     * @param $data
     * @return array|mixed|null
     */
    public function getTypeZhAttr($value, $data)
    {
        return static::getTypes($data['type']);
    }

    /**
     * 给管理员发送消息
     * @param $type
     * @param $content
     * @param null $linkurl
     * @return int|string
     */
    public function sendMessage($type, $content, $linkurl = null)
    {
        $info = [
            'uid`' => $this->id,
            'type' => $type,
            'content' => $content,
            'linkurl' => $linkurl,
        ];
        return AdminMessage::insert($info);
    }

}
