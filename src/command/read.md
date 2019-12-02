# thinkAdmin 命令行脚本

根据数据表自动生成增删改查的表单，表格等代码，一键生成

## 安装
config/console.php 中commands添加 \suframe\thinkAdmin\command\thinkAdminCURD::class

> 为了简化，取了个别名 curd   
> 文件生成默认位置 app/controller, app/ui

## 使用
命令:
```
php think curd 数据库 
php think curd news -c controller/news/manager.php  指定控制器路径(基于app/的相对路径)

# 多应用模式，生成应用下的控制器, demo为你自己app_name
 php think curd news -a demo -m -f
```

参数：
- -c --controller 指定控制器
- -a --app 指定应用
- -m --menu 生成菜单及权限(默认不生成
- -f --force 强制生成(覆盖原来生成的,已生成的菜单不会覆盖)


由于生成增删改查表单和列表需要一些中文支撑，因为字段是英文的，思考了很久，决定通过表注释自动生成
1. 表注释中： [name] ， 表注释中的name 用于显示菜单和整个栏目的名称，没有注释的表不予生成
2. 表字段注释， 开头用[name], 标识字段， [_name] 表示非公开字段，即不在列表显示，在表单显示，没有注释的字段不予生成
3. 表的类型决定了表单的生成方式，date,datetime相关类型默认日期组件，int等默认数字组件，enum默认单选框
4. 特殊字段类型，通过注释中@分割[name@type] 注释标识
 - 单图: [name@image]
 - 多图: [name@images] 需要序列化存储
 - 文件: [name@file]
 - 多文件: [name@files] 需要序列化存储
 - switch: [name@switch] ,默认0,1,
 - 滑块: [name@slider], 默认1-100
 - 滑块多选: [name@sliderRange], 默认1-100
 - 颜色选择: [name@color]
 - 评分: [name@rate]
 - 单选: [name@radio] 由于多选数据来源不固定，所有默认是空的，需要生成后自己去配置数据
 - 多选: [name@checkbox] 由于多选数据来源不固定，所有默认是空的，需要生成后自己去配置数据
 - 级联: [name@cascader] 由于级联数据来源不固定，所有默认是空的，需要生成后自己去配置数据
 - 地区: [name@city] 省市二级联动
 - 地区: [name@cityArea] 省市区三级联动
 - 日期: [name@year] ,[name@month] ,[name@dateRange] ,[name@dates]

 
 > 此工具只是帮助你处理重复工作，更细化的优化需要你自己去跳转生成的表单和表格，达到更好的展示效果    
 > 地区默认 https://unpkg.com/@form-create/data@1.0.0/dist/province_city_area.js，有兴趣的可以生成自己的然后替换    
 > 表单组件使用的http://php.form-create.com/docs/2.0/element-ui/col, 配置可以根据这个文档修改生成后的文件   
 > 有些组件是多选，需要自己处理模型里面字段的序列化存储

学会了吗？赶紧去告诉你老板，你可以10分钟生成一个现有业务的后台吧~~升职加薪赢取白富美！