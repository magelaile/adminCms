<?php
namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {

        echo 1464646;die;

    }

    public function hello()
    {
        return 'hello,' . $name;
    }
}
