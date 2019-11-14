<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminRoles extends Model
{
    //

    /**
     * 下拉options
     * @param int $parent_id
     * @param bool $hasAll
     */
    public static function buildOptions($hasAll = false)
    {
        try {
            $data = AdminRoles::field(['id', 'name'])
                ->select();
            $options = [];
            if ($hasAll) {
                $options[] = ['value' => 0, 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = ['value' => $item['id'], 'label' => $item['name']];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

}
