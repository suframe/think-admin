<?php

namespace suframe\thinkAdmin\traits;

use FormBuilder\Factory\Elm;
use suframe\form\Form;
use suframe\thinkAdmin\enum\SettingTypeEnum;
use suframe\thinkAdmin\enum\YesNoEnum;
use suframe\thinkAdmin\model\AdminSetting;
use suframe\thinkAdmin\model\AdminSettingGroup;
use think\facade\View;

/**
 * Trait SettingConfigController
 * @package suframe\thinkAdmin\traits
 */
trait SettingConfigController
{
    protected function filterGroup($model)
    {
        return $model;
    }

    protected function doShow()
    {
        $list = AdminSettingGroup::order('inx', 'asc')
            ->where('app_name', $this->getSettingAppName())
            ->order('id', 'desc')
            ->select();
        $configs = [];
        $upload_url = config('thinkAdmin.upload_url');
        foreach ($list as $item) {
            $config = [
                'key' => $item->key,
                'name' => $item->name,
                'form' => ''
            ];
            $settings = AdminSetting::order('inx', 'asc')
                ->order('id', 'desc')
                ->where('group_key', $item->key)
                ->select();
            $form = (new Form)->createElm();

            foreach ($settings as $setting) {
                $props = [];
                if ($setting['placeholder']) {
                    $props['placeholder'] = $setting['placeholder'];
                }
                $validate = [];
                if ($setting['require'] == YesNoEnum::getYes()) {
                    $validate[] = [
                        'required' => true,
                        'message' => '不能为空',
                    ];
                }
                $type = $setting->type;
                $element = null;
                switch ($type) {
                    case SettingTypeEnum::TEXT:
                    case SettingTypeEnum::DATE:
                    case SettingTypeEnum::DATETIME:
                    case SettingTypeEnum::NUMBER:
                        $element = Elm::$type(
                            $setting->key,
                            $setting->name,
                            $setting->value
                        );
                        break;
                    case SettingTypeEnum::IMAGE:
                        $element = Elm::uploadImage(
                            $setting->key,
                            $setting->name,
                            $upload_url,
                            $setting->value
                        );
                        break;
                    case SettingTypeEnum::IMAGES:
                        $element = Elm::uploadImages(
                            $setting->key,
                            $setting->name,
                            $upload_url,
                            $setting->value
                        );
                        break;
                    case 'checkbox':
                    case 'select':
                    case 'radio':
                        $element = Elm::$type(
                            $setting->key,
                            $setting->name,
                            $setting->value
                        );
                        $element->options($setting->options);
                        break;
                }
                if (!$element) {
                    continue;
                }
                $props && $element->props($props);
                $validate && $element->validate($validate);
                $form->append($element);
            }
            $config['form'] = $form;
            $configs[] = $config;
        }
        View::assign('configs', $configs);
        View::assign('active', $configs ? $configs[0]['key'] : 'active');
        return $this->fetchSettingView();
    }

    protected function fetchSettingView()
    {
        return View::fetch('system' . DIRECTORY_SEPARATOR . 'index');
    }

    protected function doPost()
    {
        $post = $this->request->post();
        $rs = false;
        foreach ($post as $key => $value) {
            $mod = AdminSetting::where('key', $key)->find();
            $mod->value = $value;
            $rs = $mod->save() || $rs;
        }
        return $this->handleResponse($rs);
    }

}