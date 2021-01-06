<?php
/**
 * +----------------------------------------------------------------------
 * | summer framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 2020 https://github.com/suframe/think-admin All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: summer <806115620@qq.com>  2020/4/30 15:02
 * +----------------------------------------------------------------------
 */

namespace suframe\thinkAdmin\tools;

class Collection extends \think\Collection
{
    public function allowKeys(array $allowed)
    {
        $result = array_flip(array_filter(array_flip($this->items), function ($key) use ($allowed) {
            return in_array($key, $allowed);
        }));
        return $result;
    }
}
