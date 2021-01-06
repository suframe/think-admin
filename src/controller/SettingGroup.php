<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\traits\CURDController;
use suframe\thinkAdmin\traits\SettingGroupController;

/**
* 商城设置分组
*/
class SettingGroup extends SystemBase
{

    use SettingGroupController;
    protected $urlPre = '/thinkadmin/settingGroup/';

}