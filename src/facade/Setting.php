<?php
namespace suframe\thinkAdmin\facade;

use think\Facade;

/**
 * Class Setting
 * @package suframe\thinkAdmin\facade
 * @mixin \suframe\thinkAdmin\Setting
 */
class Setting extends Facade
{
    protected static function getFacadeClass()
    {
        return \suframe\thinkAdmin\Setting::class;
    }
}
