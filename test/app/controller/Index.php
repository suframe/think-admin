<?php
namespace app\controller;

use app\BaseController;
use app\validate\User;
use think\db\Query;
use think\exception\ValidateException;
use think\facade\Db;
use think\response\Json;

class Index extends BaseController
{
    public function index()
    {
        $user = Db::table('admin_users');
        Db::startTrans();
        try {
            $rs = $user->select();
//            $user->delete(2);
//            throw new \Exception();
            // 提交事务
            Db::commit();
            return json()->data($rs);;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return '错误';
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
