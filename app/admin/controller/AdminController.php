<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\logic\AdminLogic;
use app\admin\logic\AdminRoleLogic;

use think\facade\View;

class AdminController extends BaseController
{

    //获取管理员列表
    public function adminList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin = new AdminLogic();
            $res = $logic_admin->getAdminList($param);
            return response_json($res);
        }

        return View::fetch();
    }

    //添加管理员
    public function adminAdd() {
        if(request()->isPost()) {

        }


        //角色列表
        $logic_admin_role = new AdminRoleLogic();
        $res_admin_role = $logic_admin_role->getRoleListAll(['field'=>"role_id,role_name"]);
        View::assign('role_arr',$res_admin_role['list']);

        return View::fetch();
    }


    //角色列表
    public function roleList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin_role = new AdminRoleLogic();
            $res = $logic_admin_role->getRoleList($param);
            if(false===$res['status']) {
                return ['code'=>-1,'msg'=>$res['msg'],'count'=>0,'data'=>[]];
            }
            return ['code'=>0,'msg'=>$res['msg'],'count'=>$res['count'],'data'=>$res['list']];
        }


        return View::fetch();
    }



}
