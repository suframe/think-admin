<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class AdminPermissions extends Model
{
    //
    static public $methods = [
        'GET' => 'GET',
        'POST' => 'POST',
        'PUT' => 'PUT',
        'PATCH' => 'PATCH',
        'DELETE' => 'DELETE',
    ];

    /**
     * 下拉options
     * @param int $parent_id
     * @param bool $hasAll
     * @param string $key
     * @return array
     */
    public static function buildOptions($parent_id = 0, $hasAll = false, $key = 'key')
    {
        try {
            $model = AdminPermissions::order('id', 'desc');
            $data = $model->select();

            $options = [];
            if ($hasAll) {
                $options[] = [$key => "0", 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = [$key => $item['id'], 'label' => $item['name']];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }
}
