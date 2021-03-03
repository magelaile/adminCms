<?php

namespace app\admin\logic;

use app\admin\model\AdminRole;

class AdminRoleLogic
{

    //获取管理员列表
    public function getRoleList($param = []) {
        list($page,$limit) = set_page_and_limit($param);

        $where = [];
        set_where_if_not_empty($where,$param,'role_name','LIKE');
        //p($where);

        $field = 'role_id,role_name,add_time,role_desc';

        $model_admin_role = new \app\admin\model\AdminRole();
        $list = $model_admin_role->field($field)->where($where)->page($page,$limit)->select()->toArray();
        $count = $model_admin_role->where($where)->count('role_id');

        return success_return('获取成功!',$list,$count);
    }

    //获取所有[符合条件]的角色
    public function getRoleListAll($param = []) {

        //查询条件、字段
        $where = [];
        $field = isset($param['field']) ? $param['field'] : "*";

        $model_admin_role = new AdminRole();
        $list = $model_admin_role->field($field)->where($where)->select()->toArray();

        return success_return('查询成功',$list);
    }



}
