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

    //菜单权限列表
    public function menuAuthList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin_auth = new \app\admin\logic\AdminAuthLogic();
            $res = $logic_admin_auth->getMeunAuthList($param);
            return response_json($res);
        }


        //$logic_admin_auth = new \app\admin\logic\AdminAuthLogic();
        //$res = $logic_admin_auth->getMeunAuthList($param);

        return View::fetch();
    }

    //菜单权限添加
    public function menuAuthAdd() {

        if(request()->isPost()) {
            $param = input();

            $logic_admin_auth = new \app\admin\logic\AdminAuthLogic();

            $data_type = trim($param['data_type']);

            if('parent_menu'==$data_type) { //获取上级菜单

                if(2==$param['auth_type']) {//添加导航
                    $param['auth_levels'] = '1';
                }elseif (3==$param['auth_type']) {//添加菜单
                    $param['auth_levels'] = '1,2';
                }elseif (4==$param['auth_type']) {//添加权限
                    $param['auth_levels'] = '1,2,3';
                }
                $res = $logic_admin_auth->getMeunAuthList($param);

                View::assign('menu_auth_list',$res['data']);
                $tpl = View::fetch('/admin/ajax/menu_auth_sel_parent');

                return response_json(success_return('获取成功',$tpl));

            }else if('controller_action'==$data_type) { //获取控制器中的方法

                $controller_name = $param['auth_c'];
                $contrl      = get_class_methods('app\admin\controller\\'.$controller_name);
                $base_contrl = get_class_methods('app\BaseController');
                $diffArray   = array_diff($contrl,$base_contrl);
                //将方法名统一转换为小写
                //$diffArray  = array_map('strtolower',$diffArray);

                p($diffArray);

                return response_json(success_return('获取成功',$result));

            }else{ //保存数据
                $res = $logic_admin_auth->addMenuAuth($param);
                return response_json($res);
            }
        }








        //获取控制器列表
        $exceptFileName = ['.','..','.svn'];
        $controller_list = get_all_filenamne(app_path().'controller',$exceptFileName,[],false);
        /*array_walk($controller_list,function (&$value,$key){
            $value = str_replace('Controller','',$value);
        });*/
        //p($controller_list);
        View::assign('controller_list',$controller_list);

        return View::fetch();
    }

    //菜单权限编辑
    public function meunAuthEdit() {


        return View::fetch();
    }

    //菜单权限删除
    public function meunAuthDel() {


        return View::fetch();
    }


}
