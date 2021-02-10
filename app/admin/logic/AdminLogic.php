<?php

namespace app\admin\logic;

use app\admin\model\Admin;

class AdminLogic
{

    //获取管理员列表
    public function getAdminList($param = []) {
        $page = intval($param['page'])>0 ? intval($param['page']) : 1;
        $limit = intval($param['limit'])>0 ? intval($param['limit']) : 20;

        $where = [
            'is_del' => 0,
        ];

        //关联模型
        $with = ['role'=>function ($query) {
            $query->field('role_id,role_name');
        }];

        $model_admin = new Admin();
        $list = $model_admin->with($with)->where($where)->page()->select()->toArray();

        return ['status'=>true,'data'=>$list,'msg'=>''];
    }


}
