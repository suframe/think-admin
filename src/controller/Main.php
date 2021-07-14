<?php

namespace suframe\thinkAdmin\controller;


use suframe\thinkAdmin\facade\Admin;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminAppsUser;
use suframe\thinkAdmin\model\AdminMessage;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\ui\table\MessageTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\Request;
use think\facade\View;

class Main extends Base
{

    /**
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $app = $this->request->get('app');
        $title = Admin::setting()->getKey('system_info.title');
        $logo = Admin::setting()->getKey('system_info.logo');
        View::assign('system_info', [
            'title' => $title['value'] ?? '管理后台',
            'welcomeUrl' => config('thinkAdmin.welcomeUrl') ?? url('/thinkadmin/main/welcome'),
            'logo' => $logo['value'] ?? 'https://t1.picb.cc/uploads/2019/10/09/gLpZna.png',
        ]);
        View::assign('admin', $this->getAdminUser());
        View::assign('app', $app);
        if($app){
            $info = AdminApps::where('app_name', $app)->find();
            View::assign('appInfo', $info);
            return View::fetch('main/index_app');
        }
        return View::fetch('main/index');
    }

    public function welcome()
    {
        return View::fetch('main/welcome');
    }

    public function message()
    {
        if ($this->request->isAjax()) {
            $rs = $this->parseSearchWhere(AdminMessage::order(
                'id', 'desc'
            ))->append(['type_zh']);
            return json_return($rs);
        }

        $table = new UITable();
        $table->createByClass(MessageTable::class);
        View::assign('table', $table);
        return View::fetch('main/message');
    }

    public function upload()
    {
        $file = request()->file('file');
        // 上传到本地服务器
        $url = \think\facade\Filesystem::disk('public')->putFile('thinkAdmin', $file);
        $url = Request::root(true) . config('filesystem.disks.public.url') . '/' . $url;
        $url = str_replace('\\', '/', $url);
        //todo 保存到数据库
        $id = 1; //存到数据库后返回id
        return json_return([
            'id' => $id,
            'filePath' => $url
        ]);
    }

    /**
     * 我的应用
     */
    public function apps()
    {
        $rs = AdminAppsUser::getAppsByUser($this->getAdminUser());

        View::assign('apps', json_encode($rs, JSON_UNESCAPED_UNICODE));
        View::assign('appsUrl', $this->urlABuild('thinkadmin/main/index', ['app' => '']));
        return View::fetch('main/apps');
    }

    /**
     * 我的菜单
     */
    public function getMyMenus()
    {
        $app = $this->request->get('app');
        $rs = AdminRoleMenu::getMenuByUser($this->getAdminUser(), true, $app);
        if (!$app && $this->getAdminUser()->isSupper()) {
            $rs = array_merge($rs, config('thinkAdmin.menus'));
        }
        return json_return(array_values($rs));
    }
}
