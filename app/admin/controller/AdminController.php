<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\logic\AdminLogic;

use think\facade\View;

class AdminController extends BaseController
{

    //获取管理员列表
    public function adminList() {
        $param = input();

        if(request()->isPost()) {

            $logic_admin = new AdminLogic();
            $res = $logic_admin->getAdminList($param);
            if(false===$res['status']) {
                return ['code'=>1,'msg'=>$res['msg'],'count'=>0,'data'=>[]];
            }
            return ['code'=>0,'msg'=>$res['msg'],'count'=>$res['count'],'data'=>$res['list']];
        }

        return View::fetch();
    }

    //添加管理员
    public function adminAdd() {
        if(request()->isPost()) {

        }

        return View::fetch();
    }



}
