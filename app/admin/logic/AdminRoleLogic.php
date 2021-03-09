<?php

namespace app\admin\logic;

use app\admin\model\AdminRole;
use think\Db;

class AdminRoleLogic
{

    //获取角色列表
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

    //获取角色信息
    public function getRoleInfo($param = []) {
        //查询条件、字段
        $where = [];
        set_where_if_not_empty($where,$param,'role_id','=');
        //p($where);
        $field = isset($param['field']) ? $param['field'] : "*";

        if(empty($where)) {
            return fail_return('查询条件错误!');
        }

        $model_admin_role = new \app\admin\model\AdminRole();
        $info = $model_admin_role->where($where)->field($field)->find();

        $info = !empty($info) ? $info->toArray() : [];

        return success_return('获取成功!',$info);
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

    //添加角色
    public function addRole($param = []) {
        $data = [
            'role_name'     => $param['role_name'],
            'role_desc'     => $param['role_desc']
        ];
        remove_space_and_eol($data);

        $validate_admin_role = new \app\admin\validate\AdminRoleValidate();
        $result = $validate_admin_role->scene('add')->check($data);
        if(false===$result){
            return fail_return($validate_admin_role->getError());
        }

        //保存数据
        $model_admin_role = new \app\admin\model\AdminRole();
        $res_inser_id = $model_admin_role->insertGetId($data);
        if($res_inser_id<=0) {
            return fail_return();
        }

        return success_return();
    }

    //编辑角色
    public function delRole($param = []) {
        //return success_return('删除成功');

        $ids = trim($param['ids'],',');

        remove_space_and_eol($ids);

        if(empty($ids)) {
            return fail_return('参数错误');
        }

        $ids_arr = explode(',',$ids);

        //排除超级管理员
        if(in_array('1',$ids_arr)) {
            return fail_return('超级管理员不能删除');
        }

        //角色是否正在被使用中
        $logic_admin = new \app\admin\logic\AdminLogic();
        $res_role_info = $logic_admin->getAdminInfo(['field'=>'user_name','role_id'=>$ids_arr]);
        //p($res_role_info['data']);
        if(!empty($res_role_info['data'])) {
            return fail_return('该角色正在被管理员：'.$res_role_info['data']['user_name'].' 使用中');
        }

        $model_admin_role = new \app\admin\model\AdminRole();
        $res_del = $model_admin_role->where('role_id','IN',$ids_arr)->delete();

        if(false===$res_del) {
            return fail_return('删除失败');
        }

        return success_return('删除成功');
    }

    //角色权限
    public function getAuthTreeData($role_auth_ids_arr = []) {

        //查询条件、字段
        $where = [];
        //set_where_if_not_empty($where,$param,'auth_ids','IN','auth_id');
        //set_where_if_not_empty($where,$param,'auth_levels','IN','auth_level');
        //p($where);
        $field = "*";

        $model_admin_auth = new \app\admin\model\AdminAuth();
        $lists = $model_admin_auth->field($field)->where($where)->order('auth_level ASC,sort DESC')->select()->toArray();

        $menu_lists = [];

        foreach($lists as $list_one) {

            $auth_id = $list_one['auth_id'];

            //跳转链接生成
            if(!empty($list_one['auth_c']) && !empty($list_one['auth_a'])) {
                $list_one['_href'] = url('/'.$list_one['auth_c'].'/'.$list_one['auth_a'])->build();
            }

            $tmp_one = [
                'title'=> $list_one['auth_name'],
                'id'=> $list_one['auth_id'],
                'field'=> '',
                'checked'=> false,
            ];

            //数据组装
            if(1==$list_one['auth_level']) { //一级菜单
                $menu_lists[$auth_id] = $tmp_one;

            }else if(2==$list_one['auth_level']) {//二级菜单
                $first_pid = $list_one['auth_pid'];
                $menu_lists[$first_pid]['children'][$auth_id] = $tmp_one;

            }else if(3==$list_one['auth_level']) {
                $auth_pid_arr = explode('-',trim($list_one['auth_pid_str'],'-'));
                $first_pid = $auth_pid_arr[0]; //所在一级id
                $second_pid = $auth_pid_arr[1];  //所在二级id
                $menu_lists[$first_pid]['children'][$second_pid]['children'][$auth_id] = $tmp_one;

            }else if(4==$list_one['auth_level']) {
                $auth_pid_arr = explode('-',trim($list_one['auth_pid_str'],'-'));
                $first_pid = $auth_pid_arr[0]; //所在一级id
                $second_pid = $auth_pid_arr[1];  //所在二级id
                $third_pid = $auth_pid_arr[2];  //所在三级id
                $menu_lists[$first_pid]['children'][$second_pid]['children'][$third_pid]['children'][$auth_id] = $tmp_one;
            }
        }

        $return_arr = $this->relatedArrToIndexArr($menu_lists);
        //p($return_arr);
        return success_return('查询成功',$return_arr);
    }

    //数组去下标处理
    public function relatedArrToIndexArr($arr=[]) {
        $arr_new = array_values($arr);
        foreach ($arr_new as &$value) {
            if(isset($value['children'])) {
                $value['children'] = $this->relatedArrToIndexArr($value['children']);
            }
        }
        return $arr_new;
    }


}
