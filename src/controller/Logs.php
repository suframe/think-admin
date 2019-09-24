<?php
namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminOperationLog;

/**
 * 管理员操作日志
 * Class Logs
 * @package suframe\thinkAdmin\controller
 */
class Logs extends Base
{

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function index()
    {
        $user_id = $this->request->param('user_id', null, 'intval');
        list($page, $nums) = $this->requestPage();
        $menu = AdminOperationLog::order('id', 'desc')->page($page, $nums);
        if($user_id){
            $menu->where('user_id', $user_id);
        }
        $list = $menu->select()->toArray();
        return json_return($list);
    }

}