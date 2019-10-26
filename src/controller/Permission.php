<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\Admin;
use suframe\thinkAdmin\model\AdminApps;
use suframe\thinkAdmin\model\AdminPermissions;
use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\ui\table\PermissionTable;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\Cache;
use think\facade\View;

class Permission extends SystemBase
{

    public function index()
    {
        if($this->request->isAjax()){
            $rs = $this->parseSearchWhere(AdminPermissions::order('id', 'desc'), [
                'name' => 'like', 'slug' => 'like', 'http_path' => 'like'
            ]);
            return json_return($rs);
        }

        $table = new UITable();
        $table->setEditOps(url('/thinkadmin/permission/eidt'), ['id']);
        $table->setDeleteOps(url('/thinkadmin/permission/delete'), ['id']);
        $table->createByClass(PermissionTable::class);
        $this->setNav('permission');
        View::assign('table', $table);
        return View::fetch('common/table');
    }

}