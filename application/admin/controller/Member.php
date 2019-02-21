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
        $user_id     = input('user_id', 0);
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

        if ($user_id) {
            $map = 'user_id=' . $user_id;
        }
        $list = Users::where($map)->field('password', true)->select();
        if (input('json', 0)) {
            return json($list);
        }
        $level = Db::name('user_level')->cache(true)->column('level_name', 'level_id');
        $this->assign('list', $list);
        $this->assign('is_valid', $is_valid);
        $this->assign('level', $level);
        return view();
    }

    public function getUserInfo($user_id = 0)
    {
        if ($user_id) {
            $map [] = ['user_id', '=', $user_id];
        } else {
            $nameorphone = input('nameorphone', 0);
            $map[]       = ['name|nickname|phone', 'like', '%' . $nameorphone . '%'];
        }

        $user = Users::where($map)->field('password', true)->find();
        if (!$user) {
            return json('无匹配结果', 404);
        }

        $level         = Db::name('user_level')->cache(true)->column('level_name', 'level_id');
        $user['level'] = $level[$user['level']];
        if (input('getcoupon')) {
            $list                = Db::name('coupon_list a')
                ->join('coupon b', 'a.cid=b.id')
                ->where(['a.user_id' => $user['user_id'], 'a.is_delete' => 0, 'b.is_delete' => 0])
                ->field('a.id,a.send_time,a.use_time,a.order_id,a.code,b.money,b.coupon_info')
                ->select();
            $user['coupon_list'] = $list;
        }
        if ($user_id)
            return $user;
        return json($user);
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
        if (!isset($data['user_id']) && strlen($data['password'])) {
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

    /**
     * 充值订单列表
     * @return \think\response\View
     */
    public function recharge()
    {
        if ($this->request->isGet()) {
            $order_id = input('order_id', 0);
            $this->assign('order_id', $order_id);
            $user_id = input('user_id', 0);
            $this->assign('user_id', $user_id);
            $points_ratio = Db::name('config')->where('name', 'points_ratio')->cache(true)->value('value');
            $this->assign('points_ratio', $points_ratio);
            return view();
        }

        $type       = input('type', false);
        $user_id    = input('user_id', 0);
        $order_id   = input('order_id', 0);
        $is_valid   = input('status', 1);
        $start_time = input('start_time', 0);
        $end_time   = input('end_time', 0);
        $search     = input('search', '');
        $p          = input('page', 1);
        $limit      = input('limit', 10);

        $field  = input('order/a', ['column' => 6, 'dir' => 'desc']);
        $fields = input('fields/a', []);
        $sort   = [];
        foreach ($field as $index => $item) {
            $sort[$fields[$item['column']]] = $item['dir'];
        }

        $map[] = ['a.status', '=', $is_valid];

        if ($type === false || $type == 'false') {
            $type  = 'false';
            $map[] = ['a.type', 'IN', '(0,1)'];
        } else {
            $map[] = ['a.type', '=', $type];
        }

        if ($order_id) {
            $map[] = ['a.order_id', '=', $order_id];
        }

        if ($user_id)
            $map[] = ['b.user_id', '=', $user_id];
        if ($start_time) {
            $map[] = ['a.pay_time', '>', strtotime($start_time)];
        }
        if ($end_time) {
            $map[] = ['a.pay_time', '<', strtotime($end_time)];
        }
        if ($search) {
            $map[] = ['b.name|b.nickname|b.phone|c.level_name', 'like', '%' . $search . '%'];
        }
        try {
            $total_amount = 0;
            $order        = ['order' => [], 'order_item' => []];
            $count        = Orders::alias('a')
                ->join('users b', 'a.user_id=b.user_id')
                ->join('user_level c', 'b.level=c.level_id')
                ->where($map)->count();
            $list         = Orders::alias('a')
                ->join('users b', 'a.user_id=b.user_id')
                ->join('user_level c', 'b.level=c.level_id')
                ->where($map)
                ->field('a.*,b.level,b.name,b.nickname,b.total_recharge,b.avatar,c.level_name')
                ->order($sort)
                ->page($p, $limit)
                ->select();
            if (!$list->isEmpty()) {
                $total_amount = array_sum(array_column($list->toArray(), 'pay_amount'));
                $order        = $this->order_detail($list[0]['order_id']);
            }

            $admin = Db::name('admin')->cache(true)->column('name,nickname', 'id');
        } catch (\Exception $e) {
            return json($e->getMessage(), 404);
        }
        //$this->assign('data', json_encode(['list' => $list, 'admin' => $admin, 'confirm_id' => session('admin.id'), 'total_amount' => $total_amount, 'type' => $type, 'order' => $order]));
        return json(['data' => $list, 'total' => $count, 'admin' => $admin]);
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

        //FIXME 注释掉，支持充值余额
        //if (count($items) == 0 || count($items) != count($serice_count)) {
        //    return json('请选择充值项目', 401);
        //}
        if (count($give) != count($give_count)) {
            return json('赠送服务项目错误', 401);
        }

        unset($data['item'], $data['serice_count'], $data['give'], $data['give_count'], $data['phone']);
        $items_info    = Db::name('item')->where('item_id', 'in', $items)->column('item_id,cate_id,cate_id2,title,price,market_price,cost,give_integral', 'item_id');
        $give_integral = 0;//赠送积分
        $order_amount  = 0;//检查应付金额
        if ($items_info)
            $order_amount = array_sum(array_column($items_info, 'price'));
        #检验前端计算订单金额和后端计算订单金额
        if ($data['total_amount'] && $data['total_amount'] != $order_amount) {
            return json('订单金额计算不符', 403);
        }

        $pay_amount = $data['pay_amount'];
        #检查店长调价
        if (isset($data['manager_reduce'])) {
            $order_amount -= floatval($data['manager_reduce']);
            $pay_amount   += floatval($data['manager_reduce']);
        }
        $time = time();
        #检查余额
        if (isset($data['use_money'])) {
            $money = Db::name('users')->where('user_id', $data['user_id'])->value('money');
            if ($data['use_money'] > $money)
                return json('会员余额不足', 401);
            $order_amount -= floatval($data['use_money']);
            $pay_amount   += floatval($data['use_money']);
        }
        #检查优惠券
        if (isset($data['coupon'])) {
            $map  = [
                'a.user_id'   => $data['user_id'],
                'a.code'      => $data['coupon'],
                'a.is_delete' => 0,
                'b.is_delete' => 0
            ];
            $info = Db::name('coupon_list a')->join('coupon b', 'a.cid=b.id')->where($map)
                ->field('a.id,a.use_time,a.send_time,a.order_id,a.cid,b.money,b.condition,b.name,b.coupon_info,b.use_start_time,b.use_end_time')
                ->find();
            if (!$info) return json('优惠券不存在', 403);
            if ($info['order_id'] > 0) return json('此优惠券已使用', 403);
            if ($info['use_start_time'] > $time || $info['use_end_time'] < $time) return json('优惠券使用期限为:' . date('Y-m-d H:i:s', $info['use_start_time']) . '至' . date('Y-m-d H:i:s', $info['use_end_time']), 403);
            if ($data['total_amount'] < $info['condition']) return json('使用此优惠券订单需满足￥' . $info['condition'] . '元', 403);
            $data['coupon_amount'] = $info['money'];
            $data['coupon_id']     = $info['id'];
            $order_amount          -= $data['coupon_amount'];
            $pay_amount            += $data['coupon_amount'];
        }
        #检查积分
        if (isset($data['use_points'])) {
            $points = Db::name('users')->where('user_id', $data['user_id'])->value('points');
            if ($data['use_points'] > $points)
                return json('会员积分不足', 401);
            $ratio                 = Db::name('config')->where('name', 'points_ratio')->cache(true)->value('value');
            $data['points_amount'] = round(intval($data['use_points']) / intval($ratio), 2);
            $order_amount          -= $data['points_amount'];
            $pay_amount            += $data['points_amount'];
        }
        if ($order_amount != $data['order_amount'] || $data['total_amount'] > $pay_amount) {
            return json('订单金额不符', 401);//总金额 > 实付金额 + 店长调价 + 余额支付 + 优惠券金额 + 积分抵扣，返回异常
        }

        #是否包含赠送项目
        if (count($give))
            $data['type'] = 1;
        $data['order_sn'] = getNewOrderSn();
        //$data['confirm_id'] = session('admin.id');
        $data['status']     = 1;
        $data['pay_status'] = 1;
        $data['add_time']   = time();
        $data['pay_time']   = time();
        Db::startTrans();
        try {
            #插入订单
            $order_id = Db::name('order')->insertGetId($data);

            #扣除余额和积分
            if (isset($data['use_money']) || isset($data['use_points'])) {
                $balance    = $data['use_money'] ?? 0;
                $use_points = $data['use_points'] ?? 0;
                account_log($data['user_id'], -1 * $balance, -1 * $use_points, '下单消费抵扣');
            }
            #优惠券抵扣
            if (isset($data['coupon']) && isset($info)) {
                Db::name('coupon_list')->where('id', $info['id'])->update(['order_id' => $order_id, 'use_time' => $time]);
                Db::name('coupon')->where('id', $info['cid'])->setInc('use_num');
            }

            #充值项目
            $item_data = [];
            foreach ($items as $index => $item) {
                $give_integral += $items_info[$item]['give_integral'];
                $item_data[]   = [
                    'order_id'      => $order_id,
                    'item_id'       => $item,
                    'cate_id'       => $items_info[$item]['cate_id'],
                    'cate_id2'      => $items_info[$item]['cate_id2'],
                    'title'         => $items_info[$item]['title'],
                    'num'           => $serice_count[$item],
                    'dec_num'       => $serice_count[$item],
                    'price'         => $items_info[$item]['price'],
                    'market_price'  => $items_info[$item]['market_price'],
                    'cost'          => $items_info[$item]['cost'],
                    'give_integral' => $items_info[$item]['give_integral'],
                    'is_give'       => 0
                ];
            }

            #如果付款金额大于充值项目，增加用户余额，赠送积分
            if ($pay_amount > $data['total_amount'] || $give_integral > 0) {
                $gap = $pay_amount - $data['total_amount'];
                account_log($data['user_id'], $gap, $give_integral, '余额充值/赠送积分');
                //checkActive();//TODO 检查充值活动
            }

            #赠送项目
            if (count($give)) {
                $items_info = Db::name('item')->where('item_id', 'in', $give)->column('item_id,cate_id,cate_id2,title,price,market_price,cost,give_integral', 'item_id');
                foreach ($give as $idx => $give_item) {
                    $item_data[] = [
                        'order_id'      => $order_id,
                        'item_id'       => $give_item,
                        'cate_id'       => $items_info[$give_item]['cate_id'],
                        'cate_id2'      => $items_info[$give_item]['cate_id2'],
                        'title'         => $items_info[$give_item]['title'],
                        'num'           => $give_count[$give_item],
                        'dec_num'       => $give_count[$give_item],
                        'price'         => $items_info[$give_item]['price'],
                        'market_price'  => $items_info[$give_item]['market_price'],
                        'cost'          => $items_info[$give_item]['cost'],
                        'give_integral' => $items_info[$give_item]['give_integral'],
                        'is_give'       => 1
                    ];
                }
            }
            if ($item_data)
                Db::name('order_item')->insertAll($item_data);

            write_order_log($order_id, session('admin.name') . '-' . session('admin.nickname'), '新增充值订单');//订单日志
            user_log('order', $data['user_id'], $order_id);//记录用户行为
            Db::name('users')->where('user_id', $data['user_id'])->update(['last_come' => $time, 'total_recharge' => Db::raw('total_recharge+' . $data['pay_amount'])]);
            Db::commit();
            return json('ok');
        } catch (Exception $e) {
            Db::rollback();
            return json($e->getMessage(), 404);
        }
    }

    public function order_detail($id = false)
    {
        if ($id === false)
            $order_id = input('order_id', 0);
        else
            $order_id = $id;
        if ($order_id < 1)
            return json('请求参数错误', 404);
        $order      = Db::name('order')->where('order_id', $order_id)->withAttr('pay_time', function ($value, $data) {
            return date('Y-m-d H:i:s', $value);
        })->find();
        $order_item = Db::name('order_item')->where('order_id', $order_id)->select();
        if ($id !== false)
            return ['order' => $order, 'order_item' => $order_item];
        return json(['order' => $order, 'order_item' => $order_item]);
    }

    /**
     * 消费列表
     * @return \think\response\Json
     */
    public function consumption()
    {
        $user_id = input('user_id', 0);
        if ($this->request->isGet()) {
            $phone = '';
            if ($user_id)
                $phone = Db::name('users')->cache(true, 864000)->where('user_id', $user_id)->value('phone');
            $this->assign('phone', $phone);
            return view();
        }

        $field  = input('order/a', ['column' => 4, 'dir' => 'desc']);
        $fields = input('fields/a', []);
        $sort   = [];
        foreach ($field as $index => $item) {
            $sort[$fields[$item['column']]] = $item['dir'];
        }

        $p          = input('page', 1);
        $limit      = input('limit', 10);
        $search     = input('search', '');
        $status     = input('status', 2);
        $start_time = input('start_time', '');
        $end_time   = input('end_time', '');
        $map[]      = ['a.is_delete', '=', 0];
        $map[]      = ['a.cid', '>', 0];
        if ($user_id)
            $map[] = ['b.user_id', '=', $user_id];

        if ($search)
            $map[] = ['b.name|b.nickname|b.phone', 'like', '%' . $search . '%'];

        $time_field = $status ? 'a.confirm_time' : 'a.schedule';
        #消费状态
        switch ($status) {
            case 0:
                $map[] = ['a.confirm_id', '=', 0];//预约码
                break;
            case 1:
                $map[] = ['a.confirm_id', '>', 0];//已消费
                break;
            case 2:
                $time_field = 'a.confirm_time|a.schedule';//全部
                break;
            default:
                array_shift($map);//无效码
                $map[]      = ['a.is_delete', '>', 0];
                $time_field = 'a.is_delete';
        }

        #时间搜索条件
        if ($start_time) {
            $end_time = $end_time ? strtotime($end_time) : time();
            $map[]    = [$time_field, 'between', [strtotime($start_time), $end_time]];
        } elseif ($end_time) {
            $map[] = [$time_field, '<', strtotime($end_time)];
        }

        $count  = Db::name('consumption a')
            ->join('users b', 'a.user_id=b.user_id')
            ->join('order_item c', 'a.item_id=c.id')
            ->where($map)
            ->count();
        $result = Db::name('consumption a')
            ->join('users b', 'a.user_id=b.user_id')
            ->join('order_item c', 'a.item_id=c.id')
            ->join('admin d', 'a.confirm_id=d.id', 'LEFT')
            ->where($map)
            ->field('IF(b.nickname <> "" AND b.nickname IS NOT null,b.nickname,b.name) as nickname,b.avatar,b.sex,b.phone,c.title,FROM_UNIXTIME(a.confirm_time) as confirm_time,FROM_UNIXTIME(a.schedule) as schedule,d.nickname as confirm,a.scroe,a.msg,a.remark,a.cid')
            ->page($p, $limit)
            ->order($sort)
            ->select();
        $result = defaultAvatar($result);
        return json(['data' => $result, 'total' => $count]);
    }

    /**
     * 预约
     * @return \think\response\Json
     */
    public function appointment()
    {
        $id         = input('post.id/d', 0);
        $index      = input('repeat/d', 0);
        $order_item = Db::name('order_item')->lock(true)->where('id', $id)->find();
        if (!$order_item)
            return json('订单信息不存在', 401);
        $map     = [
            'order_id'   => $order_item['order_id'],
            'item_id'    => $order_item['id'],
            'confirm_id' => 0,
            'is_delete'  => 0,
        ];
        $consume = Db::name('consumption')->where($map)->order('cid asc')->select();

        if (count($consume) >= $index + 1) {
            return json($consume[$index]);
        }
        if (count($consume) >= $order_item['dec_num']) {
            return json(end($consume));
        }

        if ($order_item['dec_num'] == 0)
            return json('订单所含[' . $order_item['title'] . ']项目已全部消费');
        $appointment = apponintment($order_item);
        user_log('appointment', $appointment['user_id'], $order_item['order_id']);
        return json($appointment);
    }

    public function consume($cid = null)
    {
        if (input('?post.cid') || $cid) {
            $map = ['a.cid' => $cid ?? input('post.cid/d')];
        } else {
            $map = ['a.qrcode' => input('post.qrcode'), 'a.is_delete' => 0];
        }
        $row = Db::name('consumption a')
            ->join('users b', 'a.user_id=b.user_id')
            ->join('order c', 'a.order_id=c.order_id')
            ->join('order_item d', 'a.item_id=d.id')
            ->join('item e', 'd.item_id=e.item_id')
            ->where($map)
            ->field('a.cid,a.qrcode,a.order_id,a.item_id,a.user_id,FROM_UNIXTIME(a.add_time) AS add_time,FROM_UNIXTIME(a.schedule) AS schedule,FROM_UNIXTIME(a.confirm_time) AS confirm_time,a.confirm_id,FROM_UNIXTIME(a.is_delete),a.del_remark,b.name,b.nickname,b.phone,FROM_UNIXTIME(c.pay_time) AS pay_time,IF(d.is_give>0,"赠送","购买") as is_give,d.num,d.dec_num,e.title,e.origin_image,e.description')
            ->find();
        if (!$row)
            return json('预约码错误，请确认', 401);

        if ($cid)
            return $row;
        if (input('?post.cid'))
            return json($row);
        if ($row['dec_num'] == 0)
            return json('预约码已超出消费次数', 401);
        if (strtotime($row['confirm_time']))
            return json('预约码已被消费', 401);
        try {
            Db::startTrans();
            Db::name('order_item')->where('id', $row['item_id'])->setDec('dec_num');
            #全部消费完成，修改订单状态
            if ($row['num'] == $row['dec_num'] + 1) {
                $flag = Db::name('order_item')->where("order_id={$row['order_id']} AND dec_num>0")->count();
                if ($flag == 0)
                    Db::name('order')->where('order_id')->update(['status' => 5]);
            }
            Db::name('consumption')->where('cid', $row['cid'])->update(['confirm_id' => session('admin.id'), 'confirm_time' => time()]);
            user_log('consumption', $row['user_id'], $row['cid']);//记录用户行为
            Db::commit();
            $row['dec_num'] -= 1;
            return json($row);
        } catch (Exception $e) {
            Db::rollback();
            return json($e->getMessage(), 404);
        }
    }

    public function consume_del()
    {
        $cid    = input('post.cid', 0);
        $remark = input('post.remark') ?? '管理员：' . session('admin.name') . '-' . session('admin.nickname') . ' 判定无效';
        if (!$cid)
            return json('参数错误');
        Db::name('consumption')->where('cid', $cid)->update(['is_delete' => time(), 'del_remark' => $remark]);
    }

    /**
     * 会员行为分析
     * @return \think\response\Json|\think\response\View
     */
    public function behavior()
    {
        $user_id = input('user_id') ?? 0;
        if ($this->request->isGet()) {
            $this->assign('user_id', $user_id);
            return view();
        }

        $str    = '-30 day';
        $status = input('post.status', 0);
        if ($status > 0) {
            $str = '-90 day';
            if ($status > 1)
                $str = date('Y-1-1 00:00:00');
        }
        $map = 'add_time > ' . strtotime($str) . ' AND ';
        if ($user_id) {
            $map .= 'user_id=' . $user_id;
        } else {
            $subQuery = Db::name('user_behavior')->fetchSql(true)->order('bid desc')->limit(1)->value('user_id');
            $map      .= 'user_id=(' . $subQuery . ')';
        }
        try {
            $info = Db::name('user_behavior')->where($map)->order('bid desc')->select();
            if (!$info) return json(['user' => [], 'graph_data' => [], 'info' => [], 'type' => []]);//无数据直接返回空
            $user_id = $user_id ? $user_id : $info[0]['user_id'];
            $user    = $this->getUserInfo($user_id)->toArray();
        } catch (Exception $e) {
            return json($e->getMessage(), 404);
        }

        $type           = behaviorType(0, null, true);
        $user['statis'] = [];
        $count          = count($info);
        if ($count) {
            $temp = array_count_values(array_column($info, 'type'));
            foreach ($temp as $index => $num) {
                $user['statis'][] = [$type[$index], $num];
            }
        }

        $graph_data['timeline'] = [];
        $graph_data['data']     = [];
        $recharge_map           = ['user_id' => $user_id, 'pay_status' => 1, 'status' => ['IN', '1,4,5']];
        $recharge               = Db::name('order')->cache(true, 86400)->where($recharge_map)->order('pay_time')->field('pay_amount,FROM_UNIXTIME(pay_time) as pay_time')->select();
        if ($recharge) {
            $graph_data['timeline'] = array_column($recharge, 'pay_time');
            $graph_data['data']     = array_column($recharge, 'pay_amount');
        }
        return json(['user' => $user, 'graph_data' => $graph_data, 'info' => $info, 'type' => $type]);
    }

    public function behavior_detail()
    {
        $bid      = input('bid/d', 0);
        $behavior = Db::name('user_behavior')->where('bid', $bid)->find();
        if (!$behavior)
            $this->error('参数错误');

        $item    = [1, 2, 3, 12];//商品
        $consume = [6, 7, 8];//预约消费

        $this->assign('behavior', $behavior);
        $this->assign('comment', behaviorType(0, null, 1)[$behavior['type']]);
        switch ($behavior['type']) {
            case in_array($behavior['type'], $item):
                $table    = 'item';
                $info     = Db::name($table)->where('item_id', $behavior['link_id'])->find();
                $category = Db::name('item_cate')->where('cate_id', 'IN', [$behavior['cat_id1'], $behavior['cat_id2']])->column('name', 'cate_id');
                $this->assign('item', $info);
                $this->assign('category', $category);
                return view('behavior_item');
                break;
            case in_array($behavior['type'], $consume):
                $table = 'consumption';
                $data  = $this->consume($behavior['link_id']);
                $this->assign('data', $data);
                return view('behavior_consume');
                break;
            case in_array($behavior['type'], [0, 11]):
                $this->error('内容不存在');
                break;
            default:
                $table = behaviorType(0, $behavior['type']);//order,share,activity
                if ($table == 'order') {
                    $info = $this->order_detail($behavior['link_id']);
                    //dump($info);
                    //die;
                    $this->assign('info', $info);
                }
                return view('behavior_' . $table);
        }
        return view('public/modal_404');
    }

    public function user_top()
    {
        if ($this->request->isGet())
            return view();

        $str    = '-30 day';
        $status = input('post.status', 0);
        if ($status > 0) {
            $str = '-90 day';
            if ($status > 1)
                $str = date('Y-1-1 00:00:00');
        }
        $user_ids     = [];
        $str          = strtotime($str);
        $recharge_top = Db::name('order')->cache(true, 86400)->where([['status', 'IN', '1,5'], ['pay_status', '=', 1], ['add_time', '>', $str]])
            ->group('user_id')->order('amount desc')->limit(10)
            ->field('SUM(pay_amount) as amount,user_id')->select();
        if ($recharge_top)
            $user_ids = array_column($recharge_top, 'user_id');
        //$visit    = Db::name('user_behavior')->cache(true, 86400)->where('type=1')->group('user_id')->order('count desc')->limit(10)->column('count(bid) as count', 'user_id');
        //$favorite = Db::name('user_behavior')->cache(true, 86400)->where('type=2')->group('user_id')->order('count desc')->limit(10)->column('count(bid) as count', 'user_id');
        $type   = '1, 2, 4, 6, 8, 9, 10, 11';
        $result = Db::name('user_behavior')->cache(true, 86400)
            ->where([['type', 'IN', $type], ['add_time', '>', $str]])->group('type,user_id')->order('type asc,count desc')
            ->field('count(bid) as count,user_id,type')->select();

        $top = [];
        if ($result) {
            $user_ids  = array_merge($user_ids, array_column($result, 'user_id'));
            $key       = array_column($result, 'type');
            $count_key = array_count_values($key);
            foreach (array_keys($count_key) as $index => $item) {
                $origin = array_search($item, $key);
                for ($i = 0; $i < $count_key[$item]; $i++) {
                    $top[$item][$i] = [$result[$origin + $i]['user_id'], $result[$origin + $i]['count']];
                }
            }
        }

        $type_comment = behaviorType(0, null, 1);

        $users = Db::name('users a')->cache(true, 86400)->join('user_level b', 'a.level=b.level_id')->where('a.user_id', 'IN', $user_ids)
            ->column('IF(name is null or name="",nickname,name) as name,b.level_name', 'a.user_id');
        return json(['recharge_top' => $recharge_top, 'top' => $top, 'type_comment' => $type_comment, 'users' => $users]);
    }

    public function advisory()
    {
        if ($this->request->isPost()) {
            $page  = input('post.page', 1);
            $id    = input('post.id/d', 0);
            $order = 'status ASC,add_time DESC';
            if ($id) {
                $order = "locate(a.id,'$id') DESC ," . $order;
            }
            //dump($order);die;
            $list = Db::name('advisory a')
                ->join('users b', 'a.user_id=b.user_id')
                ->field('a.*,IFNULL(b.name,b.nickname) AS name,b.avatar,b.sex,b.phone')
                ->orderRaw($order)
                ->page($page, 10)
                ->select();

            return json($list);
        }

        if ($this->request->isAjax()) {
            $id = input('id', 0);
            if (!$id) return json('参数错误');
            Db::name('advisory')->where('id', $id)->update(['status' => 1]);
            return json();
        }
        return $this->fetch();

    }
}