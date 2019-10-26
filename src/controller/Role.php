<?php

namespace suframe\thinkAdmin\controller;

use suframe\thinkAdmin\model\AdminRoles;
use suframe\thinkAdmin\ui\table\RoleTable;
use suframe\thinkAdmin\ui\UITable;
use think\facade\View;

class Role extends SystemBase
{
    /**
     * @return string|\think\response\Json
     * @throws \Exception
     */
    public function index()
    {
        if($this->request->isAjax()){
            $rs = $this->parseSearchWhere(AdminRoles::class, [
                'name' => 'like'
            ]);
            return json_return($rs);
        }

        $table = new UITable();
        $table->createByClass(RoleTable::class);
        $this->setNav('role');
        View::assign('table', $table);
        return View::fetch('common/table');
    }

}