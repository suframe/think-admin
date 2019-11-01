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
     */
    public function buildOptions($parent_id = 0, $hasAll = false)
    {
        try {
            $data = AdminMenu::where('parent_id', $parent_id)
                ->field(['id', 'title'])
                ->select();

            $options = [];
            if ($hasAll) {
                $options[] = ['value' => "0", 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = ['value' => $item['id'], 'label' => $item['title']];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

}
