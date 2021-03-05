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

        //角色列表
        $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
        $res_admin_role = $logic_admin_role->getRoleListAll(['field'=>"role_id,role_name"]);
        View::assign('role_arr',$res_admin_role['data']);
        //p($res_admin_role['data']);

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
            $logic_admin = new \app\admin\logic\AdminLogic();
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
            return response_json($res);
        }

        return View::fetch();
    }

    //添加角色
    public function roleAdd() {
        if(request()->isPost()) {
            $param = input();

            $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
            $res = $logic_admin_role->addRole($param);
            return response_json($res);
        }

        return View::fetch();
    }

    //编辑角色
    public function roleEdit() {
        if(request()->isPost()) {
            $param = input();

            $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
            $res = $logic_admin_role->editRole($param);
            return response_json($res);
        }

        $role_id = input('role_id/d',0);

        //角色信息
        $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
        $res_role_info = $logic_admin_role->getRoleInfo(['field'=>'role_id,role_name,role_desc','role_id'=>$role_id]);
        //p($role_info);
        View::assign('role_info',$res_role_info['data']);

        return View::fetch();
    }

    //删除角色
    public function roleDel() {
        $param = input();
        if(request()->isPost()) {
            $logic_admin_role = new \app\admin\logic\AdminRoleLogic();
            $res = $logic_admin_role->delRole($param);
            return response_json($res);
        }
        return response_json(fail_return());
    }

}
