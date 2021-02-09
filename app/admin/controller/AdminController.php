<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\logic\AdminLogic;

class AdminController extends BaseController
{

    //获取管理员列表
    public function adminList() {
        $param = input();
        $list = (new AdminLogic())->getAdminList($param);


        p($list);

    }



    public function hello()
    {
        return 'hello,' . $name;
    }
}
