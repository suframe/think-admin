<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\model\AdminUsers;
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
        /** @var AdminUsers $user */
        $user = app('admin')->user();
        if (!$user ||
            $user->isSupper() ||
            $this->shouldPassThrough($request)) {
            return $next($request);
        }
        /** @var \suframe\thinkAdmin\Auth $auth */
        $auth = app('admin')->auth();
        if(!$auth->check($request->pathinfo(), $request->method())){
            throw new \Exception('Permission denied', 5005);
        }

        return $next($request);
    }


}