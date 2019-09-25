<?php

namespace suframe\thinkAdmin\middleware;

use think\facade\Db;
use think\Request;

class Log
{
    public function handle(Request $request, \Closure $next)
    {
        $user = app('admin')->user();
        if($user){
            $log = [
                'user_id' => $user->id,
                'path'    => substr($request->pathinfo(), 0, 255),
                'method'  => $request->method(),
                'ip'      => $request->ip(),
                'input'   => json_encode($request->param()),
            ];
            $this->getDb()->insert($log);
        }
        return $next($request);
    }

    protected function getDb()
    {
        return Db::table(config('thinkAdmin.database.operation_log_table'));
    }
}
