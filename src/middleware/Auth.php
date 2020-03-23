<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\auth\SessionDriver;
use suframe\thinkAdmin\traits\ShouldPassThrough;
use think\Request;

class Auth
{
    use ShouldPassThrough;

    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, \Closure $next)
    {
        /** @var \suframe\thinkAdmin\Auth $auth */
        $auth = app('admin')->auth();
        $auth->initAdmin();
        if (strpos(app()->request->pathinfo(), config('thinkAdmin.uri_pre', 'thinkadmin/')) !== 0) {
            return $next($request);
        }
        if ($auth->guest() && !$this->shouldPassThrough($request)) {
            if(!$request->isAjax() && ($auth->getDriver() instanceof SessionDriver)){
                return redirect('/thinkadmin/auth/login');
            }
            throw new \Exception('need login', 5004);
        }
        return $next($request);
    }

}
