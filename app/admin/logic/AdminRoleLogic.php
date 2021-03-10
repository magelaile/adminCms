<?php

namespace app\admin\logic;

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

        $model_admin_role = new \app\admin\model\AdminRole();
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
    public function editRole($param = []) {
        $data = [
            'role_id'     => $param['role_id'],
            'role_name'   => $param['role_name'],
            'role_desc'   => $param['role_desc']
        ];

        remove_space_and_eol($data);

        $validate_admin_role = new \app\admin\validate\AdminRoleValidate();
        $result = $validate_admin_role->scene('edit')->check($data);
        if(false===$result){
            return fail_return($validate_admin_role->getError());
        }

        //保存数据
        $model_admin_role = new \app\admin\model\AdminRole();
        $res_update = $model_admin_role->update($data);
        if(!$res_update) {
            return fail_return();
        }

        return success_return();
    }

    //删除角色
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
    public function getAuthTreeData($param = []) {

        if($param['role_id']<=0) {
            return fail_return('参数错误');
        }

        //查找角色信息
        $res_role_info = $this->getRoleInfo(['field'=>'role_id,role_auth_ids','role_id'=>intval($param['role_id'])]);
        $role_auth_ids = $res_role_info['data']['role_auth_ids'];
        $role_auth_ids_arr = !empty($role_auth_ids)?explode(',',$role_auth_ids):[];
        $role_auth_ids_arr = array_flip($role_auth_ids_arr);

        //查询条件、字段
        $where = [];
        $field = 'auth_id,auth_name,auth_level,auth_pid,auth_pid_str';

        $model_admin_auth = new \app\admin\model\AdminAuth();
        $lists = $model_admin_auth->field($field)->where($where)->order('auth_level ASC,sort DESC')->select()->toArray();

        $menu_lists = [];

        foreach($lists as $list_one) {

            $auth_id = $list_one['auth_id'];

            $tmp_one = [
                'title'     => $list_one['auth_name'],
                'id'        => $auth_id,
                'field'     => '',
                'spread'    => true, //是否展开
                'checked'   => isset($role_auth_ids_arr[$auth_id]) ? true : false, //是否选中
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

    //角色权限保存
    public function editRoleAuth($param = []) {

        $ids = trim($param['ids'],',');
        remove_space_and_eol($ids);

        if($param['role_id']<=0) {
            return fail_return('参数错误!');
        }

        $ids_arr = explode(',',$ids);
        if(empty($ids_arr)) {
            return fail_return('未选择任何权限!');
        }

        $logic_admin_auth = new \app\admin\logic\AdminAuthLogic();
        $param_logic_auth = [
            'field'=>"auth_id,auth_c,auth_a,auth_type,CONCAT_WS('_',auth_c,auth_a) AS auth_c_a",
            'auth_ids'=>$ids_arr
        ];
        $auth_list = $logic_admin_auth->getMeunAuthListNormal($param_logic_auth);

        $auth_ids = array_column($auth_list['data'],'auth_id');
        $auth_ac  = array_column($auth_list['data'],'auth_c_a');

        $auth_ids_str = implode(',',$auth_ids);
        $auth_ac_str = implode(',',$auth_ac);
        //去除无效的字符
        remove_space_and_eol($auth_ac_str,['#_#,',' _#,',' _ ,','#_ ,']);
        $auth_ac_str = strtolower($auth_ac_str);
        //p($auth_ac_str);
        //进行更新
        $data = [
            'role_auth_ids' => $auth_ids_str,
            'role_auth_ac'  => $auth_ac_str
        ];

        $model_admin_role = new \app\admin\model\AdminRole();
        $res = $model_admin_role->where('role_id','=',intval($param['role_id']))->save($data);
        if(!$res) {
            return fail_return('保存失败!');
        }

        return success_return('保存成功!');
    }

}
