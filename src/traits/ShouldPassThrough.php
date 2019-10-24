<?php

namespace suframe\thinkAdmin\traits;

use think\Request;

trait ShouldPassThrough
{
    /**
     * 白名单
     * @param Request $request
     * @return bool
     */
    protected function shouldPassThrough(Request $request)
    {
        $excepts = config('thinkAdmin.auth.excepts');
        $pathInfo = $request->pathinfo();
        if($pathInfo == 'favicon.ico'){
            return true;
        }
        foreach ($excepts as $except) {
            if($pathInfo == $except){
                return true;
            }
        }
        return false;
    }
}