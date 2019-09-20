<?php

namespace suframe\thinkAdmin;

use suframe\thinkAdmin\model\AdminUsers;
use think\Collection;
use think\facade\Db;

class Auth
{
    static $instance;

    /**
     * @return Auth
     */
    public static function create()
    {
        if (static::$instance) {
            return static::$instance;
        }
        return static::$instance = new static();
    }

    /**
     * @var AdminUsers
     */
    protected $user;

    public function user()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function logout()
    {
        if (!$this->user) {
            return false;
        }
        $this->user->access_token = null;
        return $this->user->save();
    }

    /**
     * 权限检查
     * @param $http_path
     * @param string $http_method
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check($http_path, $http_method = 'GET')
    {
        $permission = $this->getUserAllPermission();
        if(!$permission){
            return false;
        }
        return !$permission->where('http_path', $http_path)
            ->where('http_method', $http_method)->isEmpty();
    }

    /**
     * 检测单独权限
     * @param $slug
     * @return bool|Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkSlug($slug)
    {
        $permission = $this->getUserAllPermission();
        if(!$permission){
            return false;
        }
        return !$permission->where('slug', $slug)->isEmpty();
    }

    /**
     * @param $token
     * @return array|bool|Db|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initByToken($token)
    {
        if (!$token) {
            return false;
        }
        $rs = $this->getUsersDb()->where('access_token', $token)->find();
        if (!$rs) {
            return false;
        }
        $user = new AdminUsers($rs);
        $this->setUser($user);
        return $user;
    }

    public function guest()
    {
        return !$this->user();
    }

    /**
     * @var Collection
     */
    protected $permission;

    /**
     * 管理员的所有权限
     * @return bool|Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getUserAllPermission()
    {
        if($this->permission){
            return $this->permission;
        }
        $user = $this->user();
        if(!$user){
            return false;
        }
        //用户权限
        $permission_ids = $this->getUserPermissionsDb()->where('user_id', $user->id)->column('permission_id');
        //用户组权限
        $role_ids = $this->getUserRolesDb()->where('user_id', $user->id)->column('role_id');
        if($role_ids){
            $permissionRole_ids = $this->getUserRolePermissionsDb()->where('role_id', 'in', $role_ids)->column('permission_id');
            if($permissionRole_ids){
                $permission_ids = array_merge($permission_ids, $permissionRole_ids);
            }
            //合并权限id
            $permission_ids = array_merge($permission_ids, $permissionRole_ids);
            $permission_ids = array_unique($permission_ids);
        }
        if(!$permission_ids){
            return false;
        }
        return $this->permission = $this->getPermissionsDb()->where('id', 'in', $permission_ids)->select();
    }

    public function addPermission($permission, $slug = null)
    {
        $slug = $slug ?: md5($permission);
        $this->permission[$slug] = $permission;
    }

    protected function getUsersDb()
    {
        return Db::table(config('thinkAdmin.database.users_table'));
    }

    protected function getUserPermissionsDb()
    {
        return Db::table(config('thinkAdmin.database.user_permissions_table'));
    }

    protected function getUserRolesDb()
    {
        return Db::table(config('thinkAdmin.database.role_users_table'));
    }

    protected function getUserRolePermissionsDb()
    {
        return Db::table(config('thinkAdmin.database.role_permissions_table'));
    }

    protected function getPermissionsDb()
    {
        return Db::table(config('thinkAdmin.database.permissions_table'));
    }
}