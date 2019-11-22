<?php
namespace app\controller;

use app\BaseController;
use app\validate\User;
use think\db\Query;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Session;
use think\response\Json;

class Index extends BaseController
{
    public function session()
    {
        if($rs = session('dddd')){
            return $rs;
        }
        session('dddd', 'sss');
        return 'dddd';
    }

    public function index()
    {
        cache('test', 'aaaa');
//        cache()->clear();
        cache()->store('temp')->clear();

//        app()->make(Cache::class, ['path' => $this->app->getRuntimePath()])
        return 'ok';
    }

    public function add()
    {
        $post = [
            'phone' => '136' . rand(10000000, 99999999),
            'nick_name' => '昵称' . time(),
        ];

        try{
            $result = validate(User::class)->batch()->check($post);
            if(true !== $result){
                dump($result);
            }
            $user = new \app\model\User();
            $rs = $user->save($post);
            return json($rs);
        }  catch (ValidateException $e) {
            // 验证失败 输出错误信息
            dump($e->getError());
        }
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
