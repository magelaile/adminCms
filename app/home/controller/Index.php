<?php
namespace app\home\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {

        p('这里是home模块');

    }

    public function hello()
    {
        return 'hello,' . $name;
    }
}
