<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminPermissions;

class AdminPermissionForm
{

    public function name()
    {
        return [
            'type' => 'input',
            'title' => '权限名称',
            'field' => 'name',
            'props' => [
                'placeholder' => '请输入权限名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function slug()
    {
        return [
            'type' => 'input',
            'title' => '标识(英文单词)',
            'field' => 'slug',
            'props' => [
                'placeholder' => '请输入标识',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function http_path()
    {
        return [
            'type' => 'input',
            'title' => '请求path',
            'field' => 'http_path',
            'props' => [
                'placeholder' => '请输入请求path',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function http_method()
    {
        $options = [];
        foreach (AdminPermissions::$methods as $key => $method) {
            $options[] = ['value' => $key, 'label' => $method];
        }
        return [
            'type' => 'select',
            'title' => '请求method',
            'field' => 'http_method',
            'options' => $options,
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function app_name()
    {
        return [
            'type' => 'input',
            'title' => '应用',
            'field' => 'app_name',
        ];
    }

}