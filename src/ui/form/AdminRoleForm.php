<?php
namespace suframe\thinkAdmin\ui\form;

class AdminRoleForm
{

    public function name()
    {
        return [
            'type' => 'input',
            'title' => '角色名称',
            'field' => 'name',
            'props' => [
                'placeholder' => '请输入名称',
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
            'title' => '标识(英文字母)',
            'field' => 'slug',
            'props' => [
                'placeholder' => '请输入标识',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

}