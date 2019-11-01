<?php
namespace suframe\thinkAdmin\ui\form;

use suframe\thinkAdmin\model\AdminRoles;

class AdminRolePermissionForm
{

    public function role_id()
    {
        $options = AdminRoles::buildOptions( true);
        return [
            'type' => 'select',
            'title' => '角色',
            'field' => 'role_id',
            'options' => $options,
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function permission_id()
    {
        return [
            'type' => 'input',
            'title' => '权限ID',
            'field' => 'permission_id',
            'props' => [
                'placeholder' => '请输入标识',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

}