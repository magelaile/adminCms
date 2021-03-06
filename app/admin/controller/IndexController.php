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
        $logic_auth = new  AdminAuthLogic();
        $res_menu_list = $logic_auth->getMenuListAll(['auth_levels'=>'1,2,3']);
        //p($res_menu_list);

        View::assign('menulist',$res_menu_list['data']);
        return View::fetch();
    }


    //主页
    public function home() {


        return View::fetch();
    }


}
