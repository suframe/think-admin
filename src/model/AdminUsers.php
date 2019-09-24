<?php

namespace suframe\thinkAdmin\model;

use think\Model;

class AdminUsers extends Model
{
    //

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
}