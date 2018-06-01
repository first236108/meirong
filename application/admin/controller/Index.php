<?php
namespace app\admin\controller;


class Index extends Base
{
    public function index()
    {
        //dump([session('admin'),session('auth'),session('adminGroup')]);
        return view();
    }

    public function systemInfo()
    {
        return view();
    }
}
