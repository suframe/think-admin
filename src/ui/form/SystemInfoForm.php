<?php
namespace suframe\thinkAdmin\ui\form;

class SystemInfoForm
{

    public function logo()
    {
        return [
            'type' => 'uploadImage',
            'title' => '站点LOGO',
            'field' => 'logo',
            'action' => config('thinkAdmin.upload_url'),
            'props' => [
                'placeholder' => '请上传LOGO',
            ]
        ];
    }

    public function title()
    {
        return [
            'type' => 'input',
            'title' => '站点名称',
            'field' => 'title',
            'props' => [
                'placeholder' => '请输入站点名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function url()
    {
        return [
            'type' => 'input',
            'title' => '站点地址',
            'field' => 'url',
            'props' => [
                'placeholder' => '请输入站点名称',
            ],
            'validate' => [
                ['required' => true, 'message' => '不能为空']
            ]
        ];
    }

    public function description()
    {
        return [
            'type' => 'input',
            'title' => '站点描述',
            'field' => 'description',
            'props' => [
                'placeholder' => '请输入站点描述',
            ]
        ];
    }

}