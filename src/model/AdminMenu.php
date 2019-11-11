<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminMenu extends Model
{
    //
    /**
     * 下拉options
     * @param int $parent_id
     * @param bool $hasAll
     * @param string $key
     * @return array
     */
    public static function buildOptions($parent_id = 0, $hasAll = false, $key = 'value')
    {
        try {
            $model = AdminMenu::order('order', 'desc');
            if ($parent_id != 'all') {
                $model->where('parent_id', $parent_id);
            }
            $data = $model->field(['id', 'title'])->select();

            $options = [];
            if ($hasAll) {
                $options[] = [$key => "0", 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = [$key => $item['id'], 'label' => $item['title']];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

}
