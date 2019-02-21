<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-12-16 上午11:14
 */

namespace app\index\controller;

use think\Db;
use think\facade\Cache;
use app\admin\model\Orders;

class User extends Base
{
    public function index()
    {
        $site_info = Db::name('config')->where("type='site_info'")->cache(true, 86400)->column('value', 'name');
        if ($this->user_id) {
            $favorite = Db::name('user_behavior')->where(['user_id' => $this->user_id, 'type' => 2, 'is_delete' => 0])->count();
            $user     = $this->user;
        } else {
            $favorite         = null;
            $user['avatar']   = 'http://image.igccc.com/FlqnB7TlnbjUbneqwUx7T9vySHGw';
            $user['nickname'] = '過路美女';
            $user['phone']    = '158****8888';
        }

        return $this->fetch('', ['user' => $user, 'site' => $site_info, 'favorite' => $favorite]);
    }

    public function login()
    {
        if ($this->request->isGet()) {
            $refer = input('refer', '');
            if ($this->token && $this->user_id) {
                if ($this->request->header('referer')) $refer = $this->request->header('referer');
                if (!$refer) $refer = url('user/index');
                header('Location:' . $refer);
                die;
            }

            $site_info = Db::name('config')->where("type='site_info'")->cache(true, 86400)->column('value', 'name');

            if (strpos($refer, '-')) $refer = url(str_replace('-', '/', $refer));
            $this->assign('refer', $refer);
            $this->assign('site', $site_info);
            return view();
        }
        $data   = input('post.');
        $result = $this->validate($data, [
            'phone'    => 'require|mobile',
            'password' => 'require|min:6'
        ], ['phone'    => '手机号码错误',
            'password' => '密码错误'
        ]);
        if (true !== $result) {
            return json(['msg' => $result], 403);
        }

        $user = Db::name('users')->where(['phone' => $data['phone'], 'is_delete' => 0])->find();
        if (!$user)
            return json(['msg' => '用户不存在'], 403);
        if ($user['is_valid'] != 1)
            return json(['msg' => '此账户已暂停使用'], 403);
        if (!password_verify($data['password'], $user['password']))
            return json(['msg' => '密码错误,请重试'], 403);
        $token = create_token('h5');
        unset($user['password']);
        Cache::set($token, $user);
        cookie('token', $token);
        Db::name('users')->where('phone', $data['phone'])->update(['token' => $token, 'last_login_time' => time()]);
        Db::name('user_behavior')->insert([
            'type'     => 0,
            'add_time' => time(),
            'user_id'  => $user['user_id']
        ]);
        return json(['token' => $token]);
    }

    public function logout()
    {
        if (!$this->token) return json('您还沒有登录哦', 403);
        Cache::rm($this->token);
        return json();
    }

    public function reg()
    {
        if ($this->request->isGet()) {
            $site_info = Db::name('config')->where("type='site_info'")->cache(true, 86400)->column('value', 'name');
            return $this->fetch('', ['site' => $site_info]);
        }
        dump(input('post.'));
    }

    public function message()
    {
        return '用户消息列表，待开发';
    }

    public function userInfo()
    {
        if ($this->request->isGet()) {
            $this->assign('user', $this->user);
            return $this->fetch();
        }

        $data = input('post.');
        if (!in_array($data['field'], ['nickname', 'name', 'phone', 'avatar'])) {
            return json('非法请求', 403);
        }
        if ($data['field'] == 'nickname' && !$data['val']) {
            return json('请输入昵称', 403);
        }
        if ($data['field'] == 'phone' && !checkMobile($data['val'])) {
            return json('手机号码错误', 403);
        }

        Db::name('users')->where('user_id', $this->user_id)->update([$data['field'] => $data['val']]);
        flashToken($this->token, $data['field'], $data['val']);
        return json();
    }

    public function modifypwd()
    {
        if ($this->request->isGet()) {
            return $this->fetch();
        }
        $data   = input('post.');
        $result = $this->validate($data, [
            'password' => 'require|min:6',
            'newpwd'   => 'require|min:6',
        ], [
            'password' => '原密码错误',
            'newpwd'   => '密码至少6位哦'
        ]);

        if (true !== $result) {
            return json($result, 403);
        }

        $user = Db::name('users')->where('user_id', $this->user_id)->find();
        if (!password_verify($data['password'], $user['password']))
            return json('原密码错误', 403);
        Db::name('users')->where('user_id', $this->user_id)->update(['password' => password_hash($data['newpwd'], PASSWORD_BCRYPT)]);
        return json();
    }

    public function checkversion()
    {
        return $this->fetch();
    }

    public function order()
    {
        if ($this->request->isGet())
            return $this->fetch();

        $p     = input('page/d', 1);
        $limit = input('pageSize/d', 5);
        $map   = [
            'user_id' => $this->user_id
        ];
        $list  = Orders::where($map)->order('add_time desc')->page($p, $limit)->column('order_id,status,pay_amount,order_sn,add_time,pay_time,pay_status,total_amount,order_amount,points_amount,coupon_amount,manager_reduce', 'order_id');
        if ($list) {
            $order_item = Db::name('order_item a')
                ->join('item b', 'a.item_id=b.item_id')
                ->where('a.order_id', 'IN', array_keys($list))
                ->field('a.*,b.origin_image')
                ->select();
            foreach ($order_item as $index => $item) {
                $list[$item['order_id']]['order_item'][] = $item;
            }
            $list = array_values($list);
        }
        if ($list)
            return json($list);
        return json($list, 403);
    }

    public function appointment()
    {
        if ($this->request->isGet())
            return $this->fetch();
        if ($this->request->isPost()) {
            $id         = input('post.id/d', 0);
            $index      = input('post.repeat/d', 0);
            $time       = input('post.time/d', time());
            $order_item = Db::name('order_item')->lock(true)->where('id', $id)->find();
            if (!$order_item) return json('您购买的服务不存在', 403);
            $map     = [
                'order_id'   => $order_item['order_id'],
                'item_id'    => $order_item['id'],
                'confirm_id' => 0,
                'is_delete'  => 0,
            ];
            $consume = Db::name('consumption')->where($map)->order('cid asc')->select();

            if (count($consume) >= $index + 1) {
                $item             = $consume[$index];
                $item['schedule'] = $time;
                Db::name('consumption')->update($item);
                return json($item);
            }
            if (count($consume) >= $order_item['dec_num']) {
                $item             = end($consume);
                $item['schedule'] = $time;
                Db::name('consumption')->update($item);
                return json($item);
            }
            if ($order_item['dec_num'] == 0)
                return json('订单所含[' . $order_item['title'] . ']项目已全部消费', 403);
            $appointment = apponintment($order_item, $this->user_id, $time);
            user_log('appointment', $this->user_id, $order_item['order_id']);

            return json($appointment);
        }
    }

    public function ajaxAppointment()
    {
        $p       = input('page/d', 1);
        $limit   = input('pageSize/d', 5);
        $map     = [
            'a.user_id'   => $this->user_id,
            'a.is_delete' => 0
        ];
        $consume = Db::name('consumption a')
            ->join('order_item b', 'a.item_id=b.id')
            ->join('item c', 'b.item_id=c.item_id')
            ->where($map)
            ->field('a.cid,a.add_time,a.schedule,a.qrcode,a.confirm_id,a.confirm_time,a.scroe,c.item_id,c.title,c.origin_image,c.description')
            ->order('confirm_id asc,schedule desc')
            ->page($p, $limit)
            ->select();

        if ($consume) return json($consume);
        return json($consume, 403);
    }

    public function comment()
    {
        if ($this->request->isGet()) {
            $cid = $this->request->get('cid');
            $this->assign('cid', $cid);
            return $this->fetch();
        }
        $data   = input('post.');
        $result = $this->validate($data, [
            'cid'   => 'require',
            'scroe' => 'require|between:1,5'
        ], [
            'cid'   => '参数错误',
            'scroe' => '请正确打分'
        ]);
        if (true !== $result) return json($result, 403);
        $flag = Db::name('consumption')->update($data);
        if (!$flag) return json('评价失败,请稍后再试', 404);
        $item_id = Db::name('consumption a')
            ->join('order_item b', 'a.item_id=b.id')
            ->where('a.cid', $data['cid'])
            ->value('b.item_id');
        user_log('comment', $this->user_id, $item_id);
        return json();
    }

    /**
     * 我的钱包 积分、余额
     * @return mixed
     */
    public function wallet()
    {
        $wallet = Db::name('users')->where('user_id', $this->user_id)->field('money,points')->find();
        $list   = Db::name('account_log')->where('user_id', $this->user_id)->order('log_id desc')->select();
        foreach ($list as $index => $item) {
            $list[$index]['gaptime'] = fixDate($item['change_time']);
        }
        return $this->fetch('', ['wallet' => $wallet, 'list' => $list]);
    }

    /**
     * 我的优惠券
     * @return mixed|\think\response\Json
     */
    public function coupon()
    {
        if ($this->request->isAjax()) {
            $map  = ['user_id' => $this->user_id, 'a.is_delete' => 0, 'b.is_delete' => 0];
            $list = Db::name('coupon_list a')->join('coupon b', 'a.cid=b.id')->where($map)
                ->field('a.id,a.cid,a.use_time,a.order_id,a.code,a.send_time,b.name,b.money,b.condition,b.use_start_time,b.use_end_time,b.coupon_info')
                ->order('a.use_time asc,a.id desc')->select();
            return json($list);
        }
        return $this->fetch();
    }

    public function photoShow()
    {
        return $this->fetch();
    }

    public function favorite()
    {
        if ($this->request->isGet()) {
            $list = Db::name('user_behavior a')
                ->join('item b', 'a.link_id=b.item_id')
                ->where(['a.user_id' => $this->user_id, 'a.type' => 2, 'a.is_delete' => 0])
                ->field('a.*,b.title,b.price,b.origin_image,b.market_price')
                ->order('a.bid desc')
                ->select();
            return $this->fetch('', ['list' => $list]);
        }
    }

    public function advisory()
    {
        if ($this->request->isPost()) {
            $data['content'] = input('post.content', '');
            if (!$data['content']) return json('请输入咨询建议内容', 403);
            $data['status']   = 0;
            $data['user_id']  = $this->user_id;
            $data['add_time'] = time();
            Db::name('advisory')->insert($data);
            return json();
        }
        if ($this->request->isAjax()) {
            $list = Db::name('advisory a')
                ->join('users b', 'a.user_id=b.user_id')
                ->where('a.user_id', $this->user_id)
                ->field('a.*,IFNULL(b.name,b.nickname) AS name,b.sex,b.phone')
                ->order('add_time desc')
                ->select();
            return json($list);
        }
        return $this->fetch();
    }
}