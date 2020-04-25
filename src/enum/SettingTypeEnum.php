<?php

namespace suframe\thinkAdmin\enum;

class SettingTypeEnum extends Base
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

}