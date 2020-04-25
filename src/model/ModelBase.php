<?php
declare (strict_types=1);

namespace suframe\thinkAdmin\model;

use think\Model;
use think\model\Relation;

/**
 * @mixin Model
 */
class ModelBase extends Model
{
    /**
     * @param bool $hasAll
     * @return array
     */
    public static function buildOptions($key, $value, $hasAll = false)
    {
        try {
            $data = static::field([$key, $value])
                ->select();
            $options = [];
            if ($hasAll) {
                $options[] = ['value' => 0, 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = ['value' => $item[$key], 'label' => $item[$value]];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param $key
     * @param $value
     * @param bool $hasAll
     * @return array
     */
    public static function buildKeyValue($key, $value, $hasAll = false)
    {
        try {
            $data = static::field([$key, $value])
                ->select();
            $options = [];
            if ($hasAll) {
                $options[] = "请选择";
            }
            foreach ($data as $item) {
                $options[$item[$key]] = $item[$value];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $relation
     * @param array $data
     */
    public function updateRelation(array $relation, array $data)
    {
        foreach ($relation as $modKey) {
            if (!$data[$modKey] || !is_array($data[$modKey])) {
                continue;
            }
            $modData = $data[$modKey];
            /** @var Relation $modRelation */
            $modRelation = $this->getRelation($modKey);
            if (!$modRelation) {
                $modRelation = $this->$modKey();
            }
            $model = $modRelation->getModel();
            $model->appendData($modData);
            $this->$modKey = $model;
        }
        $this->together($relation);
    }

}