<?php
namespace app\admin\validate;

use think\Validate;

class AdminAuthValidate extends Validate
{
    protected $rule=[
        'auth_id'       => ['require','gt:0'],
        'auth_type'     => ['require','gt:0','checkType'],
        'auth_name'     => ['require','chsAlphaNum','length'=>'2,10'],
        'auth_pid'      => ['require','gt:0'],
        'auth_pid_str'  => ['require','checkPidStr'],
        'auth_level'    => ['require','gt:0'],
        'icon_class'    => ['require'],
    ];


    protected $message=[
        'auth_id'               => '该记录信息有误~',
        'auth_type'             => '请选择类型',
        'auth_name.require'     => '账号不能为空',
        'auth_name.chsAlphaNum' => '账号只能为中文、字母和数字',
        'auth_name.length'      => '账号长度在2-10个字符之间',
        'auth_pid'              => '请选择上级菜单',
        'auth_pid_str'          => 'pid_str错误',
        'auth_level'            => '层级错误',
        'icon_class'            => '图标class不能为空'
    ];


    protected $scene = [
        //添加
        //模块
        'add_typeid_1'   =>  ['auth_type','auth_name','auth_level','auth_pid_str','icon_class'],
        //导航
        'add_typeid_2'   =>  ['auth_type','auth_name','auth_level','auth_pid','auth_pid_str','icon_class'],
        //菜单
        'add_typeid_3'   =>  ['auth_type','auth_name','auth_level','auth_pid','auth_pid_str'],
        //权限
        'add_typeid_4'   =>  ['auth_type','auth_name','auth_level','auth_pid','auth_pid_str'],

        //编辑
        //模块
        'edit_typeid_1'   =>  ['auth_id','auth_name','icon_class'],
        //导航
        'edit_typeid_2'   =>  ['auth_id','auth_name','icon_class'],
        //菜单
        'edit_typeid_3'   =>  ['auth_id','auth_name'],
        //权限
        'edit_typeid_4'   =>  ['auth_id','auth_name'],
    ];

    /* 判断上级
     * 导航 那么上级必须是模块
     * 菜单 那么上级必须是模块或者导航
     * 节点 那么上级必须是菜单
    */
    protected function checkType($value,$rule,$data) {
        if(2==$data['auth_type']) {
            if(!empty($data['auth_c']) || !empty($data['auth_a'])) {
                return '添加模块，不需要填写控制器和控制器方法';
            }

        }else if(2==$data['auth_type']) {
            if($data['auth_p_type']!=1) {
                return '添加导航，请选择模块作为上级';
            }
            if(!empty($data['auth_c']) || !empty($data['auth_a'])) {
                return '添加导航，不需要填写控制器和控制器方法';
            }

        }else if(3==$data['auth_type']) {
            if(!in_array($data['auth_p_type'],[1,2])) {
                return '添加菜单，请选择导航或者模块作为上级';
            }
            if(empty($data['auth_c']) || empty($data['auth_a'])) {
                return '添加菜单，请选择控制器和控制器方法';
            }

        }else if(4==$data['auth_type']) {
            if($data['auth_p_type']!=3) {
                return '添加节点，请选择菜单作为上级';
            }
            if(empty($data['auth_c']) || empty($data['auth_a'])) {
                return '添加节点，请选择控制器和控制器方法';
            }

        }
        return true;
    }

    /* 判断上级id组成的字符串
     * 模块 auth_pid_str为 '-'
     * 导航 auth_pid_str中只有一个上级id
     * 菜单 auth_pid_str中有一个或者两个上级id
     * 节点 auth_pid_str中有三个上级id
    */
    protected function checkPidStr($value,$rule,$data) {
        //p($value);
        if(1==$data['auth_type'] && '-'!=$value) { //模块
            return 'pid_str错误.';

        }else if(2==$data['auth_type']) { //导航
            if( count(explode('-', trim($value,'-')))!=1 || false===strpos($value,$data['auth_pid']) ) {
                return 'pid_str错误..';
            }

        }else if(3==$data['auth_type']) { //菜单
            if( count(explode('-', trim($value,'-')))<1 || false===strpos($value,$data['auth_pid']) ) {
                return 'pid_str错误...';
            }

        }else if(4==$data['auth_type']) { //节点
            if( !in_array(count(explode('-', trim($value,'-'))),[2,3]) || false===strpos($value,$data['auth_pid']) ) {
                return 'pid_str错误...';
            }
        }
        return true;
    }

}

?>

