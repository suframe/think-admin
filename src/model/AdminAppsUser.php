<?php

namespace suframe\thinkAdmin\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

/**
 * @mixin \think\Model
 */
class AdminAppsUser extends Model
{
    /**
     * @param AdminUsers $user
     * @return array|\think\Collection
     */
    public static function getAppsByUser(AdminUsers $user)
    {

        if ($user->isSupper()) {
            $appIds = 'all';
        } else {
            $appIds = AdminAppsUser::where('user_id', $user->id)
                ->field('app_id')
                ->column('app_id');
        }
        if (!$appIds) {
            return [];
        }
        try {
            $adminApps = AdminApps::order('order', 'desc')
                ->where('installed', 1)
                ->field(['app_name', 'title', 'image', 'entry']);
            if ($appIds !== 'all') {
                $adminApps->whereIn('id', $appIds);
            }
            $rs = $adminApps->select()->toArray();
            foreach ($rs as $k => $r) {
                $isUrl = (strpos($r['entry'], 'http') === 0) ||
                    (strpos($r['entry'], '//') === 0) ||
                    (strpos($r['entry'], '.html') !== false)
                ;
                $rs[$k]['entry'] = $isUrl ? $r['entry'] : url($r['entry'])->build();
            }
            return $rs;
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        return [];
    }

    public function checkUser($user_id)
    {

    }
}
