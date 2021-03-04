<?php
namespace app\admin\validate;

use think\Validate;

class AdminRoleValidate extends Validate
{
    protected $rule=[
        'role_id'       => ['require','gt:0'],
        'role_name'     => ['require','chsAlphaNum','length'=>'2,10','isNameExist'],
    ];

    protected $message=[
        'role_id'               => '该账号信息有误',
        'role_name.require'     => '名称不能为空',
        'role_name.chsAlphaNum' => '名称只能是汉字、字母和数字',
        'role_name.length'      => '账号长度在2-10个字符之间',
    ];

    //场景
    protected $scene = [
        //添加
        'add'   =>  ['role_name'],
        //编辑
        'edit'  =>  ['role_id','role_name'],
    ];

    
    //名称是否已经存在
    protected function isNameExist($value,$rule,$data) {
        $model_admin_role = new \app\admin\model\AdminRole();
        $count = $model_admin_role->where('role_name','=',$value)->count('role_id');
        //p($data);
        //添加
        if(intval($data['role_id'])<=0 && 0==$count) {
            return true;

        }else if(intval($data['role_id'])>0 && $count<=1) { //编辑
            return true;

        }else {
            return '名称已经存在';
        }
    }


}

?>

