<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminApps;

class AdminAppsForm
{

    public function type()
    {
        $options = AdminApps::getTypeOptions();
        return [
            'type' => 'select',
            'title' => '应用类型',
            'field' => 'type',
            'options' => $options,
        ];
    }

    public function app_name()
    {
        return [
            'type' => 'input',
            'title' => '应用标识',
            'field' => 'app_name',
            'props' => [
                'placeholder' => '请输入应用标识(无空格英文字母或下斜线)',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function title()
    {
        return [
            'type' => 'input',
            'title' => '应用名称',
            'field' => 'title',
            'props' => [
                'placeholder' => '请输入应用名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function entry()
    {
        return [
            'type' => 'input',
            'title' => '应用入口',
            'field' => 'entry',
            'props' => [
                'placeholder' => '请输入应用入口',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function icon()
    {
        return [
            'type' => 'uploadImage',
            'title' => '应用图标',
            'field' => 'icon',
            'action' => config('thinkAdmin.upload_url'),
            'props' => [
                'placeholder' => '请上传头像',
            ]
        ];
    }

    public function auth()
    {
        return [
            'type' => 'input',
            'title' => '开发者',
            'field' => 'auth',
            'props' => [
                'placeholder' => '请输入开发者',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function version()
    {
        return [
            'type' => 'input',
            'title' => '版本(例如：0.0.1)',
            'field' => 'version',
            'props' => [
                'placeholder' => '请输入版本',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function desc()
    {
        return [
            'type' => 'input',
            'title' => '应用描述',
            'field' => 'desc',
            'props' => [
                'placeholder' => '请输入应用描述',
            ],
        ];
    }

    public function order()
    {
        return [
            'type' => 'number',
            'title' => '排序',
            'field' => 'order',
            'value' => 100,
        ];
    }

}