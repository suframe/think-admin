<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\subscribe;

use think\Event;
use think\facade\Db;

class DebugSubscribe
{
    protected $sqls = [];

    public function HttpRun()
    {
        Db::listen(function ($sql, $runtime, $master) {
            // 进行监听处理
            $count = count($this->sqls);
            $this->sqls['sql' . $count] = $sql . ' [runtime:' . $runtime . ']';
        });
    }

    public function HttpSend($response)
    {
        $response->header($this->sqls);
    }

    public function subscribe(Event $event)
    {
        if (env('app_debug')) {
            $event->listen('HttpRun', [$this, 'HttpRun']);
            $event->listen('HttpSend', [$this, 'HttpSend']);
        }
    }
}
