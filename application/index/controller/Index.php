<?php
namespace app\index\controller;
use think\Db;
class Index
{
    public function index()
    {
        if (request()->isGet())
            return view();

        $result=Db::name('item')->select();
        if (!$result)
            return json('错误',404);

        return json($result);
    }
}
