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
        return $this->installed === 1 ? '已安装' : '未安装';
    }

}