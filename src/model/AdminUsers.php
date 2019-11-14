<?php

namespace suframe\thinkAdmin\model;

use think\Model;

/**
 * @property mixed supper
 */
class AdminUsers extends Model
{
    /**
     * 下拉options
     * @param int $parent_id
     * @param bool $hasAll
     */
    public static function buildOptions($hasAll = false)
    {
        try {
            $data = AdminUsers::field(['id', 'username', 'real_name'])
                ->select();
            $options = [];
            if ($hasAll) {
                $options[] = ['value' => 0, 'label' => "请选择"];
            }
            foreach ($data as $item) {
                $options[] = ['value' => $item['id'], 'label' => $item['real_name'] ?: $item['username']];
            }
            return $options;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function info()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'real_name' => $this->real_name,
            'avatar' => $this->avatar,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ];
    }

    public function isSupper()
    {
        return $this->supper == 1;
    }

    public function getMyRole()
    {
        return AdminRoleUsers::where('user_id', $this->id)
            ->field('role_id')
            ->column('role_id');
    }

    public function getMyMenu()
    {
        $role = $this->getMyRole();
        if (!$role) {
            return [];
        }
        return AdminRoleMenu::where('role_id', 'in', $role)
            ->select();
    }
}