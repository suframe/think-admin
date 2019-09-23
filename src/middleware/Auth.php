<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\traits\ShouldPassThrough;

class Auth
{
    use ShouldPassThrough;

    public function handle($request, \Closure $next)
    {
        if (app('admin')->auth()->guest() && !$this->shouldPassThrough($request)) {
            return json_error('need login!', 504);
        }
        return $next($request);
    }

}
