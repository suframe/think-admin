# think-admin
基于thinkphp6的管理中控台库
```
//创建thinkphp6项目
composer create-project topthink/think tp
cd tp
//tp6默认是写接口的，没有内置view,手动安装
composer require topthink/think-view

//安装think-admin
composer require suframe/think-admin:dev-master -vvv
cp .example.env .env
//编辑修改.env数据库为你自己数据库账户

// 执行sql迁移
php think migrate:run
```
注意下面这个步骤很重要

**编辑文件 app/middleware.php ,return数组里面加入(注意不是config下面的middleware.php)**

```
\think\middleware\SessionInit::class,
```
运行:
```
php think run -H 0.0.0.0
```
 访问：http://127.0.0.1:8000
