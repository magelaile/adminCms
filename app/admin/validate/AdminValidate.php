<?php
namespace app\admin\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule=[
        'admin_id'      => 'gt:0',
        'user_name'     => ['require','alphaNum','length'=>'5,10','isUserNameExist'],
        'phone_num'     => ['require','mobile'],
        'email'         => 'email',
        'role_id'       => ['require','gt:0'],
        'password'      => 'length:6,20'
    ];

    protected $message=[
        'admin_id'              => '该账号信息有误',
        'user_name.require'     => '账号不能为空',
        'user_name.alphaNum'    => '账号只能为字母和数字',
        'user_name.length'      => '账号长度在5-10个字符之间',
        'phone_num'             => '手机号错误',
        'email'                 => '邮箱错误',
        'role_id'               => '角色错误',
        'password'              => '密码长度在6-20个字符之间'
    ];


    protected $scene = [
        //添加
        'add'   =>  ['user_name','phone_num','email','role_id','password'],
        //编辑
        'edit'  =>  ['admin_id','phone_num','email','role_id','password'],
    ];




    //用户名是否已经存在
    protected function isUserNameExist($value,$rule,$data) {
        $model_admin = new \app\admin\model\Admin();
        $count = $model_admin->where('user_name','=',$value)->count('admin_id');
        if($count>0) {
            return '该账号已经存在~';
        }

        return true;
    }


}

?>

