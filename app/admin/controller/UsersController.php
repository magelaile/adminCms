<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\logic\UsersLogic;

use think\facade\View;

class UsersController extends BaseController
{

    //获取管理员列表
    public function userList() {
        $param = input();

        if(request()->isPost()) {
            $logic_users = new UsersLogic();
            $res = $logic_users->getUserList($param);
            if(false===$res['status']) {
                return ['code'=>-1,'msg'=>$res['msg'],'count'=>0,'data'=>[]];
            }
            return ['code'=>1,'msg'=>$res['msg'],'count'=>$res['count'],'data'=>$res['list']];
        }

        return View::fetch();
    }



}
