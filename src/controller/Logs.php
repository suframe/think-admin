<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminOperationLog;
use suframe\thinkAdmin\ui\table\LogsTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

/**
 * 管理员操作日志
 * Class Logs
 * @package suframe\thinkAdmin\controller
 */
class Logs extends SystemBase
{

    /**
     * @return string|\think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        if($this->request->isAjax()){
            $rs = $this->parseSearchWhere(AdminOperationLog::class, [
                'path' => 'like', 'ip' => 'like', 'create_time' => 'betweenTime',
            ]);
            return json_return($rs);
        }

        $table = new UITable();
        $table->createByClass(LogsTable::class);
        $this->setNav('logs');
        View::assign('table', $table);
        return View::fetch(config('thinkAdmin.view.commonTable'));
    }

}