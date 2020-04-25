<?php
declare (strict_types = 1);

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin Model
 */
class AdminSettingGroup extends ModelBase
{
    //
    public static function buildGroupOptions($hasAll = false)
    {
        return static::buildOptions('key', 'name', $hasAll);
    }

    public static function buildLevelKeyValue($hasAll = false)
    {
        return static::buildKeyValue('key', 'name', $hasAll);
    }
}
