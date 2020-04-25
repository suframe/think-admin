<?php

namespace suframe\thinkAdmin\ui\form;

/**
 * 商城设置分组表单
 */
class AdminSettingGroupForm
{

    public function key()
    {
        return [
            'type' => 'input',
            'title' => '分组key',
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
            'title' => '分组名称',
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