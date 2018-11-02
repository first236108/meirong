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

        $str      = '-120 day';
        $end_time = time();
        if (input('post.start_time'))
            $str = input('post.start_time');
        if (input('post.end_time'))
            $end_time = strtotime(input('post.end_time')) + 86399;

        $start_time = strtotime(date('Y-m-d', strtotime($str)));

        $map   = [
            ['status', 'IN', [1, 5]],
            ['pay_status', '=', 1],
            ['type', '=', 0],
            ['pay_amount', '>', 0],
            ['pay_time', 'between', [$start_time, $end_time]],
            ['confirm_id', '>', 0]
        ];
        $admin = Db::name('admin')->column('IF(name,name,nickname) as name', 'id');
        $data  = Db::name('order')->where($map)
            ->field('order_id, FROM_UNIXTIME(pay_time, "%Y-%m-%d") as days,confirm_id,sum(pay_amount) as pay_amount')
            ->group('days, confirm_id')
            ->order('days desc')
            ->select();
        if (!$data) return json('没有数据啦', 404);

        $confirm_ids = array_values(array_unique(array_column($data, 'confirm_id')));
        $date        = array_values(array_unique(array_column($data, 'days')));
        $series      = [];
        foreach ($date as $index => $day) {
            foreach ($data as $idx => $row) {
                if (strtotime($row['days']) < strtotime($day))
                    break;
                $series[$row['confirm_id']][$index] = $row['pay_amount'];
            }
        }

        #无业绩时填充null，图表不显示0
        foreach ($series as $index => $serie) {
            if ($temp = array_keys(array_diff_key($date, $serie))) {
                foreach ($temp as $key) {
                    $series[$index][$key] = null;
                }
            }
        }

        return json(['admin' => $admin, 'confirm_ids' => $confirm_ids, 'date' => $date, 'series' => $series, 'data' => $data, 'start_time' => date('Y-m-d', $start_time), 'end_time' => date('Y-m-d', $end_time),'sum'=>array_sum(array_column($data,'pay_amount'))]);
    }

    public function service()
    {
        if ($this->request->isGet())
            return view();

        $str      = '-120 day';
        $end_time = time();
        if (input('post.start_time'))
            $str = input('post.start_time');
        if (input('post.end_time'))
            $end_time = strtotime(input('post.end_time')) + 86399;

        $start_time = strtotime(date('Y-m-d', strtotime($str)));

        $map   = [
            ['status', 'IN', [1, 5]],
            ['pay_status', '=', 1],
            ['type', '=', 0],
            ['pay_amount', '>', 0],
            ['pay_time', 'between', [$start_time, $end_time]],
            ['confirm_id', '>', 0]
        ];
        $admin = Db::name('admin')->column('IF(name,name,nickname) as name', 'id');
        $data  = Db::name('order')->where($map)
            ->field('order_id, FROM_UNIXTIME(pay_time, "%Y-%m-%d") as days,confirm_id,sum(pay_amount) as pay_amount')
            ->group('days, confirm_id')
            ->order('days desc')
            ->select();
        if (!$data) return json('没有数据啦', 404);

        $confirm_ids = array_values(array_unique(array_column($data, 'confirm_id')));
        $date        = array_values(array_unique(array_column($data, 'days')));
        $series      = [];
        foreach ($date as $index => $day) {
            foreach ($data as $idx => $row) {
                if (strtotime($row['days']) < strtotime($day))
                    break;
                $series[$row['confirm_id']][$index] = $row['pay_amount'];
            }
        }

        #无业绩时填充null，图表不显示0
        foreach ($series as $index => $serie) {
            if ($temp = array_keys(array_diff_key($date, $serie))) {
                foreach ($temp as $key) {
                    $series[$index][$key] = null;
                }
            }
        }

        return json(['admin' => $admin, 'confirm_ids' => $confirm_ids, 'date' => $date, 'series' => $series, 'data' => $data, 'start_time' => date('Y-m-d', $start_time), 'end_time' => date('Y-m-d', $end_time),'sum'=>array_sum(array_column($data,'pay_amount'))]);
    }

    public function salary()
    {
        return view();
    }
}