<?php

namespace app\admin\logic;

use app\admin\model\Admin;

class AdminLogic
{

    //获取管理员列表
    public function getAdminList($param = []) {

        $where = [
            'is_del' => 0,
        ];

        //关联模型
        $with = ['role'=>function ($query) {
            $query->field('role_id,role_name');
        }];

        $model_admin = new Admin();
        $list = $model_admin->with($with)->where($where)->select()->toArray();

        return ['status'=>true,'data'=>$list,'msg'=>''];
    }


}
