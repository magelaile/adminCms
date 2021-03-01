<?php
namespace app\admin\controller;

use app\BaseController;
use think\App;
use think\facade\View;

class AdminController extends BaseController
{

    //获取管理员列表
    public function adminList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin = new \app\admin\logic\AdminLogic();
            $res = $logic_admin->getAdminList($param);
            return response_json($res);
        }

        return View::fetch();
    }

    //添加管理员
    public function adminAdd() {

        if(request()->isPost()) {
            $param = input();

            $logic_admin = new \app\admin\logic\AdminLogic();
            $res = $logic_admin->addAdmin($param);
            return response_json($res);
        }

        //角色列表
        $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
        $res_admin_role = $logic_admin_role->getRoleListAll(['field'=>"role_id,role_name"]);
        View::assign('role_arr',$res_admin_role['data']);

        return View::fetch();
    }

    //编辑管理员
    public function adminEdit() {
        $param = input();

        $logic_admin = new \app\admin\logic\AdminLogic();

        if(request()->isPost()) {

            $res = $logic_admin->editAdmin($param);
            return response_json($res);
        }

        $res_admin_info = $logic_admin->getAdminInfo(['admin_id'=>$param['admin_id']]);
        View::assign('admin_info',$res_admin_info['data']);
        //p($res_admin_info['data']);

        //角色列表
        $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
        $res_admin_role = $logic_admin_role->getRoleListAll(['field'=>"role_id,role_name"]);
        View::assign('role_arr',$res_admin_role['data']);
        return View::fetch();
    }

    //管理员删除
    public function adminDel() {
        $param = input();
        if(request()->isPost()) {
            $res = $logic_admin->delAdmin($param);
            return response_json($res);
        }
        return response_json(fail_return());
    }




    //角色列表
    public function roleList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
            $res = $logic_admin_role->getRoleList($param);
            if(false===$res['status']) {
                return ['code'=>-1,'msg'=>$res['msg'],'count'=>0,'data'=>[]];
            }
            return ['code'=>0,'msg'=>$res['msg'],'count'=>$res['count'],'data'=>$res['list']];
        }


        return View::fetch();
    }



}
