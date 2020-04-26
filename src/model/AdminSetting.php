<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminSetting extends ModelBase
{
    public const TEXT = 'text';
    public const NUMBER = 'number';
    public const DATE = 'date';
    public const DATETIME = 'datetime';
    public const CHECKBOX = 'checkbox';
    public const SELECT = 'select';
    public const IMAGES = 'images';
    public const IMAGE = 'image';
    public const RADIO = 'radio';

    public static function toZhArray(): array
    {

        return [
            self::TEXT => '文本',
            self::NUMBER => '数字',
            self::DATE => '日期(年月日)',
            self::DATETIME => '日期(时间)',
            self::RADIO => '单选',
            self::CHECKBOX => '多选框',
            self::SELECT => '下拉',
            self::IMAGES => '多图',
            self::IMAGE => '单图',
        ];
    }

    /**
     * 中文数组
     */
    public static function toZhArrayForSelect(): array
    {
        $arr = static::toZhArray();
        $rs = [];
        foreach ($arr as $key => $item) {
            $rs[] = ['value' => $key, 'label' => $item];
        }
        return $rs;
    }

    public const YES = 1; //是
    public const NO = 2; //否

    public static function toYesNoZhArray(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }


    /**
     * 中文数组
     */
    public static function toYesNoZhArrayForSelect(): array
    {
        $arr = static::toYesNoZhArray();
        $rs = [];
        foreach ($arr as $key => $item) {
            $rs[] = ['value' => $key, 'label' => $item];
        }
        return $rs;
    }


    /**
     * 获取分组
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AdminSettingGroup::class, 'group_key', 'key');
    }

    public function getGroupTextAttr()
    {
        return $this->group()->value('name');
    }

    public function getRequireTextAttr()
    {
        return $this->require_enum->getZhName();
    }

    public function getRequireEnumAttr($key, $data)
    {
        return (new YesNoEnum($data['require']));
    }

    public function getOptionsAttr($key, $data)
    {
        if (!$data['values']) {
            return [];
        }
        $values = explode("\n", $data['values']);
        $rs = [];
        foreach ($values as $value) {
            if (strpos($value, ':') === false) {
                $rs[] = $value;
            } else {
                $value = explode(':', $value);
                $rs[] = [
                    'label' => $value[1],
                    'value' => $value[0],
                ];
            }
        }
        return $rs;
    }

    public static function cateOnBeforeWrite($mod)
    {
        if ($mod->values) {
            $mod->values = str_replace("\r", '', $mod->value);
        }
    }

    protected $jsonValueKey = ['images', 'checkbox'];

    /**
     * @param $value
     * @param $data
     * @return mixed
     */
    public function setValueAttr($value, $data)
    {
        if (in_array($data['type'], $this->jsonValueKey)) {
            return $value ? json_encode($value, JSON_UNESCAPED_UNICODE) : '{}';
        }
        return $value;
    }

    public function getValueAttr($value, $data)
    {
        if (in_array($data['type'], $this->jsonValueKey)) {
            return $value ? json_decode($value, true) : [];
        }
        return $value;
    }
}
