# think-admin
基于thinkphp6的管理中控台库

开发交流QQ群：647344518   [立即加群](http://shang.qq.com/wpa/qunwpa?idkey=83a58116f995c9f83af6dc2b4ea372e38397349c8f1973d8c9827e4ae4d9f50e)     
体验地址： [thinkadmin.zacms.com](http://thinkadmin.zacms.com)  账户：admin,密码：admin,**请勿乱更改信息**

## 安装
```
//设置阿里云composer镜像：
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

//创建thinkphp6项目
composer create-project topthink/think tp
cd tp
```

修改composer.json（重要）
这步很重要，因为suframe-think还在开发版，没有发布正式版，只能先安装dev版
增加到底部如下
```
{
...
    {
      "minimum-stability": "dev",
      "prefer-stable": true
    }
...
}

```

```
//安装think-admin
composer require suframe/think-admin:dev-master -vvv
cp .example.env .env
//编辑修改.env数据库为你自己数据库账户
// 执行sql迁移
php think migrate:run
```
注意下面这个步骤很重要

**编辑文件 app/middleware.php ,return数组里面加入**

```
\think\middleware\SessionInit::class,
```

然后去 config/middleware.php 里面的 priority里面加上相同的项目,这里是因为这个中间件执行比较靠前，需要提前初始化
```
'priority' => [
    \think\middleware\SessionInit::class,
],
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

## 根据mysql表增删改查自动生成
当已经有数据库了，需要开发增删改查，还要去撸代码？
作为喜欢偷懒的我，当然不可能写增删改查，这辈子都不可能，我要自动生成！
一觉醒来，上帝听到我的呼唤，于是有了根据mysql表增删改查自动生成增删改查

部署：
在 config/console.php 文件增加

```
// 指令定义
'commands' => [
    \suframe\thinkAdmin\command\thinkAdminCURD::class
],
```

新建表
```
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[ID]',
  `name` varchar(128) NOT NULL COMMENT '[标题]',
  `image` varchar(255) DEFAULT NULL COMMENT '[封面@image]',
  `cid` int(11) DEFAULT NULL COMMENT '[分类@cascader]',
  `publish_time` datetime NOT NULL COMMENT '[发布日期]',
  `created_time` timestamp NULL DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='[新闻]';
```

进入命令行，网站根目录，运行命令
```
# 生成模型
php think make:model News
# 生成curl
php think curd news
```
进入后台，增加个菜单，注意天下icon,然后刷新下页面看看吧(接下来也会自动生成，开发中)

![7](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/7.jpg)
![6](https://raw.githubusercontent.com/suframe/think-admin/master/asserts/6.jpg)

觉得不错上方点个star，
更详细的数据库设置教程和文档参看：[生成增删改文档](https://github.com/suframe/think-admin/blob/master/src/command/read.md)
