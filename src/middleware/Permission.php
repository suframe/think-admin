<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\traits\ShouldPassThrough;
use think\Request;

class Permission
{
    use ShouldPassThrough;

    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, \Closure $next)
    {
        if (config('thinkAdmin.check_route_permission') === false) {
            return $next($request);
        }

        $user = app('admin')->user();
        if (!$user || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if(!app('admin')->auth()->check($request->pathinfo(), $request->method())){
            throw new \Exception('Permission denied');
        }

        return $next($request);
    }


}