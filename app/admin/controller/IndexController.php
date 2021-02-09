<?php
namespace app\admin\controller;

use app\BaseController;

class IndexController extends BaseController
{
    public function index()
    {

        p('这里是admin模块');

    }

    public function hello()
    {
        return 'hello,' . $name;
    }
}
