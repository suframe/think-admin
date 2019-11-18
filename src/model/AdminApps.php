<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminApps extends Model
{
    //
    public function getInstalledNameAttr()
    {
        return $this->isInstalled() ? '已安装' : '未安装';
    }

    public function isInstalled()
    {
        return $this->installed === 1;
    }

}