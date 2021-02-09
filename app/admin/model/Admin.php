<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{

    //关联查询角色名称
    public function role() {
        return $this->hasOne(AdminRole::class,'role_id','role_id');
    }


}
