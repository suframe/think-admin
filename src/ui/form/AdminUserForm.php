<?php
namespace suframe\thinkAdmin\ui\form;

class AdminUserForm
{

    public function real_name()
    {
        return [
            'type' => 'input',
            'title' => '真实姓名',
            'field' => 'real_name',
            'props' => [
                'placeholder' => '请输入名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function avatar()
    {
        return [
            'type' => 'uploadImage',
            'title' => '头像',
            'field' => 'avatar',
            'action' => config('thinkAdmin.upload_url'),
            'props' => [
                'placeholder' => '请上次头像',
            ]
        ];
    }

    public function password()
    {
        return [
            'type' => 'password',
            'title' => '密码',
            'field' => 'password',
            'props' => [
                'placeholder' => '密码',
            ],
            'callback' => function($element){
                $element->showPassword(true);
                return $element;
            }
        ];
    }

    public function repassword()
    {
        return [
            'type' => 'password',
            'title' => '重复输入密码',
            'field' => 'repassword',
            'props' => [
                'placeholder' => '重复输入密码',
            ],
            'callback' => function($element){
                $element->showPassword(true);
                return $element;
            }
        ];
    }

}