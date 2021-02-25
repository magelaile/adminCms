<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{

    //关联查询角色名称
    public function role() {
        return $this->hasOne(AdminRole::class,'role_id','role_id');
    }




    //状态获取器
    public function getAdminStatusAttr($value) {
        $status = [
                -1 => ['value' => -1,'name' => '禁用'],
                1  => ['value' => 1,'name' => '正常'],
            ];
        return $status[$value];
    }


}
