<?php

namespace app\admin\logic;

class AdminLogic
{

    //获取管理员列表
    public function getAdminList($param = []) {
        list($page,$limit) = set_page_and_limit($param);

        $where = [
            ['is_del','=',0],
        ];
        set_where_if_not_empty($where,$param,'user_name','=');
        set_where_if_not_empty($where,$param,'phone_num','=');
        set_where_time($where,$param,'add_time','start_time','end_time');

        //关联模型
        $with = ['role'=>function ($query) {
            $query->field('role_id,role_name');
        }];

        $model_admin = new \app\admin\model\Admin();
        $list = $model_admin->with($with)->where($where)->page($page,$limit)->select()->toArray();
        $count = $model_admin->with($with)->where($where)->count('admin_id');
        //echo $model_admin->getLastSql();die;

        return success_return('获取成功!',$list,$count);
    }

    //获取管理员信息
    public function getAdminInfo($param = []) {

        //查询条件、字段
        $where = [];
        set_where_if_not_empty($where,$param,'admin_id','=');

        $field = isset($param['field']) ? $param['field'] : "*";

        if(empty($where)) {
            return fail_return('查询条件错误!');
        }

        $model_admin = new \app\admin\model\Admin();
        $info = $model_admin->where($where)->field($field)->find();

        $info = !empty($info) ? $info->toArray() : [];

        return success_return('获取成功!',$info);
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


    //管理员编辑
    public function editAdmin($param = []) {
        $data = [
            'admin_id'      => $param['admin_id'],
            'phone_num'     => $param['phone_num'],
            'email'         => $param['email'],
            'role_id'       => $param['role_id'],
            'password'      => $param['password'],
            'admin_status'  => $param['admin_status'],
        ];

        remove_space_and_eol($data);

        $validate_admin = new \app\admin\validate\AdminValidate();
        $result = $validate_admin->scene('edit')->check($data);
        if(false===$result){
            return fail_return($validate_admin->getError());
        }

        $res_admin_info = $this->getAdminInfo(['admin_id'=>$data['admin_id'],'field'=>'admin_id,salt']);
        if(false===$res_admin_info['status'] || empty($res_admin_info['data'])) {
            return fail_return('账号信息错误');
        }
        //p($res_admin_info['data']);

        //处理密码
        if(!empty($data['password'])) {
            $data['password'] = md100($data['password'],$res_admin_info['data']['salt']);
        }else{
            unset($data['password']);
        }

        //p($data);die;
        //unset($data['admin_id']);

        $model_admin = new \app\admin\model\Admin();

        try {
            // 这里是主体代码
            $res_update = $model_admin->update($data);
            //p($res_update->toArray());

            return success_return('保存成功');

        } catch (\Exception $e) {
            // 这是进行异常捕获
            //print_r($e->getMessage());die;
            return fail_return($e->getMessage());
        }
    }

    //删除管理员
    public function delAdmin($param = []) {
        return success_return('删除成功');

        $ids = trim($param['ids'],',');

        remove_space_and_eol($ids);

        if(empty($ids)) {
            return fail_return('参数错误');
        }

        $ids_arr = explode(',',$ids);

        //排除超级管理员
        if(in_array('1',$ids_arr)) {
            return fail_return('删除失败');
        }

        $model_admin = new \app\admin\model\Admin();
        $res_del = $model_admin->where('admin_id','IN',$ids_arr)->delete();

        if(false===$res_del) {
            return fail_return('删除失败');
        }

        return success_return('删除成功');
    }


}
