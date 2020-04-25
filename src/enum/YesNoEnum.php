<?php

namespace suframe\thinkAdmin\enum;

class YesNoEnum extends Base
{

    public const YES = 1; //是
    public const NO = 2; //否

    public static function toZhArray(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }

    public static function getYes()
    {
        return static::YES;
    }

    public static function getNo()
    {
        return static::NO;
    }

    public function isYes()
    {
        return $this->getValue() == self::YES;
    }

}