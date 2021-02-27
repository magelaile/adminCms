<?php

namespace app\admin\logic;

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

        $model_admin = new \app\admin\model\Admin();
        $list = $model_admin->with($with)->where($where)->page($page,$limit)->select()->toArray();
        $count = $model_admin->with($with)->where($where)->count('admin_id');

        return success_return('获取成功!',$list,$count);
    }


    //管理员添加
    public function addAdmin($param = []) {
        $data = [
            'user_name'     => $param['user_name'],
            'phone_num'     => $param['phone_num'],
            'email'         => $param['email'],
            'role_id'       => $param['role_id'],
            'password'      => !empty($param['password']) ? $param['password'] : '123456', //密码默认123456
            'admin_status'  => $param['admin_status'],
        ];

        remove_space_and_eol($data);


        $validate_admin = new \app\admin\validate\AdminValidate();
        $result = $validate_admin->scene('add')->check($data);
        if(false===$result){
            return fail_return($validate_admin->getError());
        }


        $data['salt']       = get_str_by_len(4);
        $data['password']   = md100($data['password'],$data['salt']);
        $data['add_time']   = time();

        //保存数据
        $model_admin = new \app\admin\model\Admin();
        $res_inser_id = $model_admin->insertGetId($data);
        if($res_inser_id>0) {
            return success_return();
        }

        return fail_return();
    }

}
