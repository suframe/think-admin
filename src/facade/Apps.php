<?php
namespace suframe\thinkAdmin\facade;

use think\Facade;

/**
 * Class Apps
 * @package suframe\thinkAdmin\facade
 * @mixin \suframe\thinkAdmin\Apps
 */
class Apps extends Facade
{
    protected static function getFacadeClass()
    {
        return \suframe\thinkAdmin\Apps::class;
    }
}
