# think-admin
基于thinkphp6的管理中控台库

QQ群：647344518   [立即加群](http://shang.qq.com/wpa/qunwpa?idkey=83a58116f995c9f83af6dc2b4ea372e38397349c8f1973d8c9827e4ae4d9f50e)     
体验地址： [thinkadmin.zacms.com](http://thinkadmin.zacms.com)  账户：admin,密码：admin,**请勿乱更改信息**

## 安装
```
//设置阿里云composer镜像：
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

//创建thinkphp6项目
composer create-project topthink/think tp
cd tp
//tp6默认是写接口的，没有内置view,手动安装
composer require topthink/think-view

//安装think-form表单库(由于think-admin及相关库还在开发测试中，没有发布正式版，仅供测试)
composer require suframe/form:dev-master -vvv

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
## 运行:
```
php think run -H 0.0.0.0
```
## 访问
 http://127.0.0.1:8000

预览
![1](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/1.png)

![2](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/2.png)

![3](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/3.png)

![4](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/4.png)

![5](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/5.png)