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
            $favorite = Db::name('user_behavior')->where(['user_id' => $this->user_id, 'type' => 2])->count();
            $user     = $this->user;
        } else {
            $favorite         = null;
            $user['avatar']   = 'http://image.igccc.com/FlqnB7TlnbjUbneqwUx7T9vySHGw';
            $user['nickname'] = '過路美女';
            $user['phone']    = '158****8888';
        }

        $this->assign('user', $user);
        $this->assign('site', $site_info);
        $this->assign('favorite', $favorite);
        return view();
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
        return $this->fetch();
    }

    public function message()
    {
        //todo
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
        $limit = input('pageSize/d', 10);
        $map   = [
            'user_id' => $this->user_id
        ];
        $list  = Orders::where($map)->order('add_time desc')->page($p, $limit)->column('order_id,status,pay_amount,order_sn,add_time,pay_time,pay_status,order_amount,points_amount,coupon_amount,manager_reduce', 'order_id');
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
        return json($list);
    }

    public function appointment()
    {

    }
}