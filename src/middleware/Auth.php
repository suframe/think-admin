<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\traits\ShouldPassThrough;

class Auth
{
    use ShouldPassThrough;

    public function handle($request, \Closure $next)
    {
        $redirectTo = admin_base_path(config('thinkAdmin.auth.redirect_to', 'admin/core/auth/login'));
        if (app('admin')->auth()->guest() && !$this->shouldPassThrough($request)) {
            return redirect($redirectTo);
        }
        return $next($request);
    }

}
