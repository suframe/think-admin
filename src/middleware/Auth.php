<?php

namespace suframe\thinkAdmin\middleware;

use suframe\thinkAdmin\traits\ShouldPassThrough;

class Auth
{
    use ShouldPassThrough;

    public function handle($request, \Closure $next)
    {
        if (app('admin')->auth()->guest() && !$this->shouldPassThrough($request)) {
            throw new \Exception('need login', 5004);
        }
        return $next($request);
    }

}
