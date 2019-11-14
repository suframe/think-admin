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
     * @param $user_id
     * @return array|\think\Collection
     */
    public static function getAppsByUser($user_id)
    {
        $appIds = AdminAppsUser::where('user_id', $user_id)
            ->field('app_id')
            ->column('app_id');
        if (!$appIds) {
            return [];
        }
        try {
            $rs = AdminApps::order('order', 'desc')
                ->field(['app_name', 'title', 'icon', 'entry'])
                ->whereIn('id', $appIds)
                ->select()->toArray();
            foreach ($rs as $k => $r) {
                $isUrl = (strpos($r['entry'], 'http') === 0) ||
                    (strpos($r['entry'], '//') === 0);
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
