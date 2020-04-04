<?php

namespace suframe\thinkAdmin\traits;

use suframe\thinkAdmin\model\AdminRoleMenu;
use think\App;
use think\Request;

/**
 * Trait AppSetNav
 * @package suframe\thinkAdmin\traits
 * @property App $app
 * @property Request $request
 * @method getAdminUser
 * @method setAdminNavs($navs, $active)
 */
trait AppSetNav
{
    /**
     * 获取app菜单
     * @param null $active
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function setNav($active = null)
    {
        $appName = $this->app->http->getName();
        $urlHtmlSuffix = '.' . config('route.url_html_suffix');
        $menu = AdminRoleMenu::getMenuByUser($this->getAdminUser(), true, $appName);
        if ($menu) {
            $menu = array_pop($menu);
        }
        $navs = [];
        foreach ($menu['child'] as $item) {
            $key = str_replace($urlHtmlSuffix, '', $item['uri']);
            $key = explode('/', $key);
            if (count($key) > 4) {
                array_pop($key);
            }
            $key = array_pop($key);
            $navs[$key] = [$item['title'], $item['uri'], $item['icon']];
        }
        $key = str_replace($urlHtmlSuffix, '', $this->request->pathinfo());
        $key = explode('/', $key);
        if (count($key) > 2) {
            array_pop($key);
        }
        $active = array_pop($key);
        $this->setAdminNavs($navs, $active);
    }

}