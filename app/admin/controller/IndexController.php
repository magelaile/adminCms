<?php
namespace app\admin\controller;

use app\BaseController;
use app\admin\logic\AdminAuthLogic;
use think\facade\View;



class IndexController extends BaseController
{

    //首页
    public function index() {
        //获取左侧菜单
        $menu_list = (new AdminAuthLogic())->getMenuList();

        View::assign('menulist',$menu_list);

        return View::fetch();
    }


}
