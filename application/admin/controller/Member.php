<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/10 0:25
 */

namespace app\admin\controller;

use think\Db;
use app\admin\model\Users;
use app\admin\model\Orders;
use think\Exception;

class Member extends Base
{
    public function Index()
    {
        if (request()->isGet())
            return view();
        $type        = input('type', 0);
        $is_valid    = input('is_valid', 1);
        $create_time = input('create_time', 0);
        $last_come   = input('last_come', 0);
        $nameorphone = input('nameorphone', 0);
        $map         = [
            ['is_delete', '=', $type],
            ['is_valid', '=', $is_valid]
        ];

        if ($create_time) {
            $map[] = ['create_time', '>', strtotime($create_time)];
        }
        if ($last_come) {
            $map[] = ['last_come', '>', strtotime($last_come)];
        }
        if ($nameorphone) {
            $map[] = ['name|nickname|phone', 'like', '%' . $nameorphone . '%'];
        }

        try {
            $list  = Users::where($map)->field('password', true)->order('last_come desc')->find();
            $level = Db::name('user_level')->cache(true)->column('level_name', 'level_id');
        } catch (\Exception $e) {
            return json($e->getMessage(), 403);
        }
        return json([$list, $level]);
    }

    public function addEditMember()
    {
        $data   = input('post.');
        $result = $this->validate($data, [
            'phone'    => 'require|mobile|unique:users',
            'password' => 'min:6',
        ], [
            'phone.require' => '手机号码必填',
            'phone.mobile'  => '手机号码错误',
            'phone.unique'  => '手机号码已注册',
            'password'      => '密码长度至少6位'
        ]);
        if (true !== $result) {
            return json($result, 401);
        }

        $user = new Users;
        if (!isset($data['password']) && strlen($data['password'])) {
            $data['password'] = '123456';
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if (!isset($data['user_id'])) {
            $user->create_time = time();
            $user->save($data);
        } else {
            $user->isUpdate(true)->save($data);
        }

        return json($user->user_id);
    }

    public function recharge()
    {
        if (request()->isGet())
            return view();
        $type        = input('type', 0);
        $is_valid    = input('status', 1);
        $start_time  = input('start_time', 0);
        $end_time    = input('end_time', 0);
        $nameorphone = input('nameorphone', 0);
        $map         = [
            ['a.type', '=', $type],
            ['a.status', '=', $is_valid]
        ];

        if ($start_time) {
            $map[] = ['a.pay_time', '>', strtotime($start_time)];
        }
        if ($end_time) {
            $map[] = ['a.pay_time', '<', strtotime($end_time)];
        }
        if ($nameorphone) {
            $map[] = ['b.name|b.nickname|b.phone|c.level_name', 'like', '%' . $nameorphone . '%'];
        }

        try {
            $sum_amount   = 0;
            $total_amount = 0;
            $list         = Orders::alias('a')
                ->join('__USERS__ b', 'a.user_id=b.user_id')
                ->join('__USER_LEVEL__ c', 'b.level=c.level_id')
                ->where($map)
                ->field('a.*,b.level,b.name,b.nickname,b.total_recharge,b.avatar,c.level_name')
                ->order('a.add_time desc')
                ->paginate(20);

            $page = $list->render();
            if ($list) {
                $list         = $list->items();
                $sum_amount   = array_sum(array_column($list, 'pay_amount'));
                $total_amount = Orders::where('order_id', 'in', array_column($list, 'order_id'))->sum('pay_amount');
            }
            $admin = Db::name('admin')->cache(true)->column('name', 'id');
        } catch (\Exception $e) {
            return json($e->getMessage(), 403);
        }
        return json([$list, $admin, $page, $sum_amount, $total_amount]);
    }

    public function addOrder()
    {
        $data     = input('post.');
        $validate = new \app\admin\validate\Item;
        if (!$validate->check($data)) {
            return json($validate->getError(), 401);
        }

        $items        = input('post.item/a', []);
        $serice_count = input('post.serice_count/a', []);
        $give         = input('post.give/a', []);
        $give_count   = input('post.give_count/a', []);

        if (count($items) == 0 || count($items) != count($serice_count)) {
            return json('请选择充值项目', 401);
        }
        if (count($give) != count($give_count)) {
            return json('赠送服务项目错误', 401);
        }

        unset($data['item'], $data['serice_count'], $data['give'], $data['give_count'], $data['phone']);
        $result = $data['pay_amount'];

        isset($data['use_money']) && $result += floatval($data['use_money']);
        isset($data['manager_reduce']) && $result += floatval($data['manager_reduce']);
        if (isset($data['use_points'])) {
            $ratio                 = Db::name('config')->cache('config')->where('name=points_ratio')->value('value');
            $data['points_amount'] = intval($data['use_points']) * intval($ratio);
            $result                += $data['points_amount'];
        }
        if ($data['total_amount'] > $result) {
            return json('订单金额不符', 401);
        }

        $data['order_sn'] = getNewOrderSn();
        Db::startTrans();
        try {
            $order_id = Db::name('order')->insertGetId($data);

            #充值项目
            $items_info = Db::name('item')->where('item_id', 'in', $items)->column('item_id,cate_id,cate_id2,title,num,price,market_price,cost,give_integral', 'item_id');
            $item_data  = [];
            foreach ($items as $index => $item) {
                $item_data[] = [
                    'order_id'      => $order_id,
                    'item_id'       => $item,
                    'cate_id'       => $items_info[$item]['cate_id'],
                    'cate_id2'      => $items_info[$item]['cate_id2'],
                    'title'         => $items_info[$item]['title'],
                    'num'           => $serice_count[$item],
                    'price'         => $items_info[$item]['price'],
                    'market_price'  => $items_info[$item]['market_price'],
                    'cost'          => $items_info[$item]['cost'],
                    'give_integral' => $items_info[$item]['give_integral'],
                ];
            }

            #赠送项目
            if (count($give)) {
                $items_info = Db::name('item')->where('item_id', 'in', $give)->column('item_id,cate_id,cate_id2,title,num,price,market_price,cost,give_integral', 'item_id');
                foreach ($give as $idx => $give_item) {
                    $item_data[] = [
                        'order_id'      => $order_id,
                        'item_id'       => $give_item,
                        'cate_id'       => $items_info[$give_item]['cate_id'],
                        'cate_id2'      => $items_info[$give_item]['cate_id2'],
                        'title'         => $items_info[$give_item]['title'],
                        'num'           => $serice_count[$give_item],
                        'price'         => $items_info[$give_item]['price'],
                        'market_price'  => $items_info[$give_item]['market_price'],
                        'cost'          => $items_info[$give_item]['cost'],
                        'give_integral' => $items_info[$give_item]['give_integral']
                    ];
                }
            }

            Db::name('order_item')->insertAll($item_data);
            write_order_log($order_id, session('admin.name') . '-' . session('admin.nickname'), '新增充值订单');
            return json('ok');
        } catch (Exception $e) {
            Db::rollback();
            return json($e->getMessage(), 404);
        }
    }
}