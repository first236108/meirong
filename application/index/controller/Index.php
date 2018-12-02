<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        if (request()->isGet()) {
            $recommend = Db::name('item')->where("is_recommend=1 AND is_delete=0 AND on_sale=1")->order('sort desc')->select();
            $this->assign('recommend', $recommend);
            return view();
        }

        $page   = input('page/d', 1);
        $size   = input('pagesize/d', 2);
        $result = Db::name('item')->where("is_delete=0 AND on_sale=1")->limit($size)->page($page)->select();
        if (!$result)
            return json('错误', 404);

        return json($result);
    }

    public function detail()
    {
        $id  = input('id', 0);
        $row = Db::name('item')->where("item_id={$id} AND on_sale=1 AND is_delete=0")->find();
        if (!$row)
            $this->error('您要查看的页面没找到哦!~');

        $row['img'] = Db::name('item_img')->where('item_id', $id)->order('sort desc')->select();
        $this->assign('row', $row);
        return $this->fetch();
    }
}
