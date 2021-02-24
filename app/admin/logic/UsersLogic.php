<?php

namespace app\admin\logic;

use app\model\Users;

class UsersLogic
{

    //获取管理员列表
    public function getUserList($param = []) {
        $page = intval($param['page'])>0 ? intval($param['page']) : 1;
        $limit = intval($param['limit'])>0 ? intval($param['limit']) : 20;

        $where = [

        ];

        $model_users = new Users();
        $list  = $model_users->where($where)->page($page,$limit)->select()->toArray();
        $count = $model_users->where($where)->count('user_id');

        return ['status'=>true,'list'=>$list,'count'=>$count,'msg'=>''];
    }


}
