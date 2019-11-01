<?php
namespace suframe\thinkAdmin\ui\form;

class AdminSettingForm
{

    public function group()
    {
        return [
            'type' => 'input',
            'title' => '配置组',
            'field' => 'group',
            'props' => [
                'placeholder' => '请输入配置组',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function key()
    {
        return [
            'type' => 'input',
            'title' => 'KEY(英文标识)',
            'field' => 'key',
            'props' => [
                'placeholder' => '请输入KEY',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function name()
    {
        return [
            'type' => 'input',
            'title' => '配置名称',
            'field' => 'name',
            'props' => [
                'placeholder' => '请输入配置名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function value()
    {
        return [
            'type' => 'input',
            'title' => '配置值',
            'field' => 'value',
            'props' => [
                'placeholder' => '请输入配置值',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
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