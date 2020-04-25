<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminSettingGroup;
use suframe\thinkAdmin\model\AdminSetting;

class AdminSettingForm
{

    public function group_key()
    {
        return [
            'type' => 'select',
            'options' => AdminSettingGroup::buildGroupOptions(true),
            'title' => '分组',
            'field' => 'group_key',
            'validate' => [
                ['required' => true, 'message' => '必选']
            ]
        ];
    }

    public function key()
    {
        return [
            'type' => 'input',
            'title' => '配置key',
            'field' => 'key',
            'validate' =>
                [
                    [
                        'required' => true,
                        'message' => '不能为空',
                    ],
                ],
        ];
    }

    public function name()
    {
        return [
            'type' => 'input',
            'title' => '配置名称',
            'field' => 'name',
            'validate' =>
                [
                    [
                        'required' => true,
                        'message' => '不能为空',
                    ],
                ],
        ];
    }

    public function type()
    {
        return [
            'type' => 'radio',
            'title' => '类型',
            'field' => 'type',
            'options' => AdminSetting::toZhArrayForSelect(),
            'validate' => [
                ['required' => true, 'message' => '必选']
            ]
        ];
    }

    public function values()
    {
        return [
            'type' => 'textarea',
            'title' => '选择配置项',
            'field' => 'values',
            'props' => [
                'placeholder' => '下拉，多选框配置项，key:value一行一个，',
            ],
        ];
    }

    public function default_value()
    {
        return [
            'type' => 'input',
            'title' => '默认值',
            'field' => 'default_value',
        ];
    }

    public function placeholder()
    {
        return [
            'type' => 'input',
            'title' => '提示信息',
            'field' => 'placeholder',
        ];
    }

    public function require()
    {
        return [
            'type' => 'radio',
            'options' => AdminSetting::toYesNoZhArrayForSelect(),
            'title' => '是否必选',
            'field' => 'require',
            'value' => 1,
        ];
    }

    public function inx()
    {
        return [
            'type' => 'number',
            'title' => '排序',
            'field' => 'inx',
            'value' => 100,
        ];
    }

}