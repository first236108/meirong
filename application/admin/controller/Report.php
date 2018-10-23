<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/10 0:25
 */

namespace app\admin\controller;

use think\Db;

class Report extends Base
{
    public function income()
    {
        if ($this->request->isGet())
            return view();
        $str        = '-7 day';
        $start_time = strtotime(date('Y-m-d', strtotime($str)));
        $end_time   = time();
        $map        = [
            ['status', 'IN', [1, 5]],
            ['pay_status', '=', 1],
            ['type', '=', 0],
            ['pay_amount', '>', 0],
            ['pay_time', '>', $start_time]
        ];
        $data       = Db::name('order')->fetchSql(true)->where($map)->field('SUM(pay_amount),pay_time,confirm_id')->group('confirm_id')->select();
        dump($data);
        die;
    }

    public function service()
    {
        return view();
    }

    public function salary()
    {
        return view();
    }
}