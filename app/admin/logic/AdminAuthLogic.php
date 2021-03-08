<?php

namespace app\admin\logic;

class AdminAuthLogic {

    /*获取权限菜单列表
    */
    public function getMeunAuthList($param = []) {
        $where = [];

        $model_admin_auth = new \app\admin\model\AdminAuth();
        $lists = $model_admin_auth->where($where)->order('auth_level,sort ASC')->select()->toArray();

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
        $lists = $model_admin_auth->field($field)->where($where)->order('auth_level,sort ASC')->select()->toArray();

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
    public function addMeunAuth($param = []) {
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

        $validate_admin = new \app\admin\validate\AdminAuthValidate();
        $result = $validate_admin->scene('add_typeid_'.$data['type_id'])->check($data);
        if(false===$result){
            return fail_return($validate_admin->getError());
        }

        p($param);


        //保存数据
        $model_admin_auth = new \app\admin\model\AdminAuth();
        $res_inser_id = $model_admin_auth->insertGetId($data);
        if($res_inser_id<=0) {
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
}
