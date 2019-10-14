<?php

namespace suframe\thinkAdmin\controller;


use think\facade\Request;
use think\facade\View;

class Main extends Base
{

    public function index()
    {

        View::assign('admin', $this->getAdminUser());
        return View::fetch('main/index');
    }

    public function message()
    {
        return View::fetch('main/message');
    }

    public function upload()
    {
        $file = request()->file('file');
        // 上传到本地服务器
        $url = \think\facade\Filesystem::disk('public')->putFile( 'thinkAdmin', $file);
        $url = Request::root(true) . config('filesystem.disks.public.url') . '/' . $url;
        //todo 保存到数据库
        $id = 1; //存到数据库后返回id
        return json_return([
            'id' => $id,
            'filePath' => $url
        ]);
    }
}