<?php

namespace app\admin\logic;

use app\admin\model\AdminRole;

class AdminRoleLogic
{

    //获取管理员列表
    public function getRoleList($param = []) {
        $page = intval($param['page'])>0 ? intval($param['page']) : 1;
        $limit = intval($param['limit'])>0 ? intval($param['limit']) : 20;

        $where = [
            'is_del' => 0,
        ];

        //关联模型
        $with = ['role'=>function ($query) {
            $query->field('role_id,role_name');
        }];

        $model_admin_role = new AdminRole();
        $list = $model_admin->with($with)->where($where)->page($page,$limit)->select()->toArray();
        $count = $model_admin->with($with)->where($where)->count('admin_id');

        return ['status'=>true,'list'=>$list,'count'=>$count,'msg'=>''];
    }

    //获取所有[符合条件]的角色
    public function getRoleListAll($param = []) {

        //查询条件、字段
        $where = [];
        $field = isset($param['field']) ? $param['field'] : "*";

        $model_admin_role = new AdminRole();
        $list = $model_admin_role->field($field)->where($where)->select()->toArray();

        return ['status'=>true,'list'=>$list,'count'=>0,'msg'=>''];
    }



}
