<?php
namespace app\home\controller;

use app\BaseController;

class IndexController extends BaseController
{
    public function index()
    {

        p('-----------------------',false);
        p('正在开发中~');

    }

    public function hello()
    {
        return 'hello,' . $name;
    }
}
