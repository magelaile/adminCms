<?php

namespace app\admin\logic;

use app\admin\model\AdminAuth;

class AdminAuthLogic {

    //获取菜单列表
    public function getMenuList() {

        $map = [];
        /*if('all'!=$role_auth_ids){
            $map['auth_id'] = array('in',$role_auth_ids);
        }*/
        //【注意：排序必须首先以auth_level升序排序】
        $model_admin_auth = new AdminAuth();
        $lists = $model_admin_auth->where($map)->order('auth_level,sort ASC')->select()->toArray();

        //p($lists);

        $menu_lists = [];

        foreach($lists as $list_one) {
            $auth_id = $list_one['auth_id'];
            $auth_level = $list_one['auth_level'];

            $tmp_one = [
                'auth_id'   => $list_one['auth_id'],
                'auth_name' => $list_one['auth_name'],
                'auth_pid'  => $list_one['auth_pid'],
                'auth_c'    => $list_one['auth_c'],
                'auth_a'    => $list_one['auth_a'],
                'icon_class'=> $list_one['icon_class'],
            ];
            //跳转链接生成
            if(!empty($list_one['auth_c']) && !empty($list_one['auth_a'])) {
                $tmp_one['_href'] = url('/'.$list_one['auth_c'].'/'.$list_one['auth_a'])->build();
            }


            if(1==$auth_level) { //一级菜单
                $menu_lists[$auth_id] = $tmp_one;

            }elseif(2==$auth_level) {//二级菜单
                $pid = $list_one['auth_pid'];
                $menu_lists[$pid]['children'][$auth_id] = $tmp_one;

            }elseif(3==$auth_level) {
                $auth_pid_arr = explode('-',trim($list_one['auth_pid_str'],'-'));
                $ppid = $auth_pid_arr[0]; //所在一级id
                $pid = $auth_pid_arr[1];  //所在二级id
                $menu_lists[$ppid]['children'][$pid]['children'][$auth_id] = $tmp_one;
            }
        }

        return $menu_lists;
    }





}
