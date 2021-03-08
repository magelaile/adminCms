<?php

namespace app\admin\logic;

class AdminAuthLogic {

    /*获取权限菜单列表
    */
    public function getMeunAuthList($param = []) {

        $res_menu_list = $this->getMenuListAll($param);
        if(!$res_menu_list['status']) {
            return $res_menu_list;
        }
        //p($res_menu_list['data']);

        $lists_all = $this->handleMenuAuthArr($res_menu_list['data']);
        //p($lists_all);

        return success_return('获取成功!',$lists_all);

    }

    /* 获取所有权限列表并按层级分组
     */
    public function getMenuListAll($param = []) {

        //查询条件、字段
        $where = [];
        set_where_if_not_empty($where,$param,'auth_ids','IN','auth_id');
        set_where_if_not_empty($where,$param,'auth_levels','IN','auth_level');
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

            //数据组装
            if(1==$list_one['auth_level']) { //一级菜单
                $menu_lists[$auth_id] = $list_one;

            }else if(2==$list_one['auth_level']) {//二级菜单
                $first_pid = $list_one['auth_pid'];
                $menu_lists[$first_pid]['children'][$auth_id] = $list_one;

            }else if(3==$list_one['auth_level']) {
                $auth_pid_arr = explode('-',trim($list_one['auth_pid_str'],'-'));
                $first_pid = $auth_pid_arr[0]; //所在一级id
                $second_pid = $auth_pid_arr[1];  //所在二级id
                $menu_lists[$first_pid]['children'][$second_pid]['children'][$auth_id] = $list_one;

            }else if(4==$list_one['auth_level']) {
                $auth_pid_arr = explode('-',trim($list_one['auth_pid_str'],'-'));
                $first_pid = $auth_pid_arr[0]; //所在一级id
                $second_pid = $auth_pid_arr[1];  //所在二级id
                $third_pid = $auth_pid_arr[2];  //所在三级id
                $menu_lists[$first_pid]['children'][$second_pid]['children'][$third_pid]['children'][$auth_id] = $list_one;
            }
        }

        return success_return('查询成功',$menu_lists);
    }

    /*将权限列表多维数组按照层级顺序处理为一级数组
    */
    public function handleMenuAuthArr($arr) {

        $lists_all = [];

        foreach ($arr as $value1) {
            $temp = $value1;
            if(isset($temp['children'])) {
                unset($temp['children']);
            }
            $lists_all[] = $temp;

            foreach ($value1['children'] as $value2) {
                $temp = $value2;
                if(isset($temp['children'])) {
                    unset($temp['children']);
                }
                $lists_all[] = $temp;

                foreach ($value2['children'] as $value3) {
                    $temp = $value3;
                    if(isset($temp['children'])) {
                        unset($temp['children']);
                    }
                    $lists_all[] = $temp;

                    foreach ($value3['children'] as $value4) {
                        $temp = $value4;
                        if(isset($temp['children'])) {
                            unset($temp['children']);
                        }
                        $lists_all[] = $temp;
                    }
                }
            }
        }

        return $lists_all;
    }

    /*权限保存
    */
    public function addMenuAuth($param = []) {
        $data = [
            'auth_name'     => $param['auth_name'],
            'auth_type'     => $param['auth_type'],
            'auth_pid'      => $param['auth_pid'],
            'auth_c'        => $param['auth_c'],
            'auth_a'        => $param['auth_a'],
            'sort'          => $param['sort'],
            'icon_class'    => $param['icon_class'],
        ];

        remove_space_and_eol($data);

        //上级信息
        $data['auth_p_type'] = 0;
        $data['auth_level'] = 1;
        $data['auth_pid_str'] = '-';
        if($data['auth_pid']>0) {
            $parent_auth_info = $this->getAuthInfo(['auth_id'=>$data['auth_pid']]);
            $data['auth_p_type'] = $parent_auth_info['data']['auth_type'];
            $data['auth_pid_str'] = $parent_auth_info['auth_pid_str'].$parent_auth_info['auth_id'].'-';
        }

        //权限层级
        if(1==$data['auth_type']) { //模块
            $data['auth_level'] = 1;
        }else if (2==$data['auth_type']) { //导航
            $data['auth_level'] = 2;
        }else if (3==$data['auth_type']) { //菜单
            if(1==$data['auth_p_type']) {
                $data['auth_level'] = 2; //二级菜单
            }else if(2==$data['auth_p_type']){
                $data['auth_level'] = 3; //二级菜单
            }
        }else if(4==$data['auth_type']) { //节点
            $data['auth_level'] = 4;
        }

        //验证
        $validate_admin_auth = new \app\admin\validate\AdminAuthValidate();
        $result = $validate_admin_auth->scene('add_typeid_'.$data['auth_type'])->check($data);
        if(false===$result){
            return fail_return($validate_admin_auth->getError());
        }
        //p($data);

        empty($data['auth_c']) && $data['auth_c'] = '#';
        empty($data['auth_a']) && $data['auth_a'] = '#';

        //保存数据
        $model_admin_auth = new \app\admin\model\AdminAuth();
        $res_insert = $model_admin_auth->save($data);  //save方法默认只写入数据表已经有的字段
        if(!$res_insert) {
            return fail_return();
        }

        return success_return();
    }

    /*权限编辑
    */
    public function editMeunAuth($param = []) {

    }

    /*权限删除
    */
    public function delMeunAuth($param = []) {

    }

    /*获取权限菜单信息
    */
    public function getAuthInfo($param = []) {

        //查询条件、字段
        $where = [];
        set_where_if_not_empty($where,$param,'auth_id','=');
        //p($where);
        $field = isset($param['field']) ? $param['field'] : "*";

        if(empty($where)) {
            return fail_return('查询条件错误!');
        }

        $model_admin_auth = new \app\admin\model\AdminAuth();
        $info = $model_admin_auth->where($where)->field($field)->find();

        $info = !empty($info) ? $info->toArray() : [];

        return success_return('获取成功!',$info);
    }

}
