<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;

class Apps extends Base
{
    /**
     * apps列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $installed = $this->request->param('installed', 'intval');
        $app = AdminApps::order('order', 'asc')
            ->order('id', 'desc');
        if($installed){
            $app->where('installed', $installed);
        }
        $rs = $app->select();
        return json_return($rs);
    }

    /**
     * 检测新app
     * @return \think\response\Json
     */
    public function checkNewApp()
    {
        $rs = Admin::apps()->checkNewApp();
        return $rs ? json_success() : json_error();
    }

    /**
     * 安装
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function install()
    {
        $app_name = $this->requireParam('app_name');
        $rs = Admin::apps()->install($app_name);
        return $rs ? json_success() : json_error();
    }

    /**
     * 卸载
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function remove()
    {
        $app_name = $this->requireParam('app_name');
        $rs = Admin::apps()->remove($app_name);
        return $rs ? json_success() : json_error();
    }

    /**
     * 删除
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function delete()
    {
        $app_name = $this->requireParam('app_name');
        $rs = Admin::apps()->remove($app_name);
        return $rs ? json_success() : json_error();
    }

}