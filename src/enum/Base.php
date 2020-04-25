<?php
declare (strict_types = 1);

namespace suframe\thinkAdmin\enum;

use MyCLabs\Enum\Enum;

abstract class Base extends Enum
{

    /**
     * 中文数组
     */
    public static function toZhArray(): array
    {
        return [];
    }

    public function getZhName(): string
    {
        $zh = static::toZhArray();
        if(!$zh){
            return '';
        }
        return $zh[$this->getValue()] ?? '';
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

}