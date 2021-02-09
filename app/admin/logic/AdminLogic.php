<?php

namespace app\admin\logic;

use app\admin\model\Admin;

class AdminLogic {

    //获取管理员列表
    public function getAdminList($param=[]) {
        $model_admin = new Admin();

        //关联模型
        $with = ['role'=>function($query){
            $query->field('role_id,role_name');
        }];

        $list = $model_admin->with($with)->select()->toArray();

        return $list;


    }




}
