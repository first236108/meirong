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

class User extends Base
{
    public function index()
    {
        return view();
    }

    public function login()
    {
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
}