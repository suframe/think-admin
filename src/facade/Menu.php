<?php
namespace suframe\thinkAdmin\facade;

use think\Facade;

/**
 * Class Menu
 * @package suframe\thinkAdmin\facade
 * @mixin \suframe\thinkAdmin\Menu
 */
class Menu extends Facade
{
    protected static function getFacadeClass()
    {
        return \suframe\thinkAdmin\Menu::class;
    }
}
