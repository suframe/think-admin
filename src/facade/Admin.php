<?php
namespace suframe\thinkAdmin\facade;

use think\Facade;

/**
 * Class Admin
 * @package suframe\thinkAdmin\facade
 * @mixin \suframe\thinkAdmin\Admin
 */
class Admin extends Facade
{
    protected static function getFacadeClass()
    {
        return \suframe\thinkAdmin\Admin::class;
    }
}
