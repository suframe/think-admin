<?php

namespace suframe\thinkAdmin;

use suframe\thinkAdmin\auth\SessionDriver;
use suframe\thinkAdmin\model\AdminMenu;
use suframe\thinkAdmin\model\AdminRoleMenu;
use suframe\thinkAdmin\model\AdminRoleUsers;
use suframe\thinkAdmin\model\AdminUsers;
use suframe\thinkAdmin\traits\SingleInstance;
use think\Collection;
use think\facade\Cache;
use think\facade\Db;

class Auth
{
    use SingleInstance;

    protected $driver;

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

    /**
     * 登录
     * @param $username
     * @param $password
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Exception
     */
    public function login($username, $password)
    {
        $rs = $this->getUsersDb()->where('username', $username)->find();
        if (!$rs) {
            throw new \Exception('用户名错误');
        }
        $user = new AdminUsers($rs);
        $user->exists(true);
        //最大登录失败错误次数
        $max_fail = config('thinkAdmin.auth.max_fail', 10);
        if ($user->login_fail >= $max_fail) {
            throw new \Exception("超过最大登录错误次数限制{$user->login_fail}/{$max_fail}!");
        }
        $passwordHash = $this->hashPassword($password);
        if ($user->password !== $passwordHash) {
            $user->login_fail += 1;
            $user->save();
            throw new \Exception('密码错误');
        }
        $user->login_fail = 0;
        $user->save();
        return $this->getDriver()->login($user);
    }

    public function logout()
    {
        if (!$this->user) {
            return false;
        }
        return $this->getDriver()->logout($this->user);
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

        if (!$permission) {
            return false;
        }
        $rs = !$permission->where('http_path', $http_path)
            ->where('http_method', $http_method)->isEmpty();

        if (!$rs) {
            //匹配通配符*
            $likes = $permission
                ->whereLike('http_path', '*')
                ->where('http_method', $http_method)
                ->filter(function ($item) use ($http_path) {
                    $path = str_replace('*', '', $item['http_path']);
                    return strpos($http_path, $path) !== false;
                });
            $rs = !$likes->isEmpty();
        }
        return $rs;
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
        if (!$permission) {
            return false;
        }
        return !$permission->where('slug', $slug)->isEmpty();
    }

    /**
     * 初始化用户
     * @return mixed
     */
    public function initAdmin()
    {
        $user = $this->getDriver()->initAdmin($this->getUsersDb());
        if ($user) {
            $admin = new AdminUsers($user);
            $admin->exists(true);
            $this->setUser($admin);
            return $admin;
        }
    }

    public function guest()
    {
        return !$this->user();
    }

    /**
     * 获取管理员菜单
     * @return Collection|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminMenu()
    {
        $roleIds = AdminRoleUsers::where('user_id', $this->user()->getKey())->column('role_id');
        if (!$roleIds) {
            return json_return([]);
        }
        $menuIds = AdminRoleMenu::where('role_id', 'in', $roleIds)->column('menu_id');
        if (!$menuIds) {
            return json_return([]);
        }
        return AdminMenu::where('id', 'in', $menuIds)
            ->order('order', 'desc')
            ->order('id', 'desc')
            ->select();
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
        if ($this->permission) {
            return $this->permission;
        }
        $user = $this->user();
        if (!$user) {
            return false;
        }
        //缓存
        if (config('thinkAdmin.cache_admin_permission', false)) {
            $menu = Cache::get('thinkAdmin.admin.menus');
            if ($menu) {
                return $this->permission = $menu;
            }
        }

        //用户权限
        $permission_ids = $this->getUserPermissionsDb()->where('user_id', $user->id)->column('permission_id');
        //用户组权限
        $role_ids = $this->getUserRolesDb()->where('user_id', $user->id)->column('role_id');
        if ($role_ids) {
            $permissionRole_ids = $this->getUserRolePermissionsDb()->where('role_id', 'in',
                $role_ids)->column('permission_id');
            if ($permissionRole_ids) {
                $permission_ids = array_merge($permission_ids, $permissionRole_ids);
            }
            //合并权限id
            $permission_ids = array_merge($permission_ids, $permissionRole_ids);
            $permission_ids = array_unique($permission_ids);
        }
        if (!$permission_ids) {
            return false;
        }
        $this->permission = $this->getPermissionsDb()->where('id', 'in', $permission_ids)->select();
        //缓存
        if (config('thinkAdmin.cache_admin_permission', false)) {
            Cache::tag('thinkAdmin')->set('thinkAdmin.admin.menus', $this->permission);
        }
        return $this->permission;
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

    public function hashPassword($password)
    {
        $hash = config('thinkAdmin.auth.passwordHashFunc');
        if (!$hash) {
            $salt = config('thinkAdmin.auth.passwordSalt', 'thinkAdmin');
            return md5(md5($password . $salt));
        }
        return $hash($password);
    }

    /**
     * 密码强度
     * @param $str
     */
    public function judgePassword($str)
    {
        $score = 0;
        if (preg_match("/[0-9]+/", $str)) {
            $score++;
        }
        if (preg_match("/[0-9]{3,}/", $str)) {
            $score++;
        }
        if (preg_match("/[a-z]+/", $str)) {
            $score++;
        }
        if (preg_match("/[a-z]{3,}/", $str)) {
            $score++;
        }
        if (preg_match("/[A-Z]+/", $str)) {
            $score++;
        }
        if (preg_match("/[A-Z]{3,}/", $str)) {
            $score++;
        }
        if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $str)) {
            $score += 2;
        }
        if (preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/", $str)) {
            $score++;
        }
        if (strlen($str) >= 10) {
            $score++;
        }
        return $score;
    }

    /**
     * @param mixed $driver
     */
    public function setDriver($driver): void
    {
        $this->driver = $driver;
    }

    /**
     * 认证驱动
     * @return mixed
     */
    public function getDriver()
    {
        if ($this->driver) {
            return $this->driver;
        }
        $driver = config('thinkAdmin.auth.driver', SessionDriver::class);
        return $this->driver = new $driver;
    }
}