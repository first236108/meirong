<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 19-02-15 上午10:17
 */

namespace app\admin\controller;

use think\Db;

class Advertising extends Base
{
    public function carousel()
    {
        if ($this->request->isPost()) {

        }
        if ($this->request->isAjax()) {
            $list = Db::name('carousel')->where('is_delete', 0)->order('sort desc')->select();
            return json($list);
        }

        return $this->fetch();
    }

    public function coupon()
    {
        return $this->fetch();
    }
}