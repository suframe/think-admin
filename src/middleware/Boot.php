<?php

namespace suframe\thinkAdmin\middleware;

class Boot
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}
