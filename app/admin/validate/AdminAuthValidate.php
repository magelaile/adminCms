<?php
namespace app\admin\validate;

use think\Validate;

class AdminAuthValidate extends Validate
{
    protected $rule=[
        'auth_id'       => ['require','gt:0'],
        'auth_type'     => ['require','gt:0','checkParentInfo'],
        'auth_name'     => ['require','chsAlphaNum','length'=>'2,10'],
        'auth_pid'      => ['require','gt:0'],
        'auth_c'        => ['require','chsAlphaNum'],
        'auth_a'        => ['require','chsAlphaNum'],
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
        'auth_c'                => '请选择控制器名称',
        'auth_a'                => '请选择控制器方法',
        'auth_level'            => '层级错误',
        'icon_class'            => '图标class不能为空'
    ];


    protected $scene = [
        //添加
        //模块
        'add_typeid_1'   =>  ['auth_type','auth_name','auth_level','icon_class'],
        //导航
        'add_typeid_2'   =>  ['auth_type','auth_name','auth_level','auth_pid','icon_class'],
        //菜单
        'add_typeid_3'   =>  ['auth_type','auth_name','auth_level','auth_pid','auth_c','auth_a'],
        //权限
        'add_typeid_4'   =>  ['auth_type','auth_name','auth_level','auth_pid','auth_c','auth_a'],

        //编辑
        //模块
        'edit_typeid_1'   =>  ['auth_id','auth_type','auth_name','auth_level','icon_class'],
        //导航
        'edit_typeid_2'   =>  ['auth_id','auth_type','auth_name','auth_level','auth_pid','icon_class'],
        //菜单
        'edit_typeid_3'   =>  ['auth_id','auth_type','auth_name','auth_level','auth_pid','auth_c','auth_a'],
        //权限
        'edit_typeid_4'   =>  ['auth_id','auth_type','auth_name','auth_level','auth_pid','auth_c','auth_a'],
    ];

    /* 判断上级
     * 添加导航 那么上级必须是模块
     * 添加菜单 那么上级必须是模块或者导航
     * 添加节点 那么上级必须是菜单
    */
    protected function checkParentInfo($value,$rule,$data) {
        if(2==$data['auth_type'] && $data['auth_p_type']!=1) {
            return '添加导航，请选择模块作为上级';

        }else if(3==$data['auth_type'] && !in_array($data['auth_p_type'],[1,2])) {
            return '添加菜单，请选择导航或者模块作为上级';

        }else if(4==$data['auth_type'] && $data['auth_p_type']!=3) {
            return '添加节点，请选择菜单作为上级';
        }
        return true;
    }

}

?>

