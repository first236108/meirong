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
            $list  = Users::where($map)->field('password', true)->order('last_come desc')->select();
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
            $map[] = ['a.add_time', '>', strtotime($start_time)];
        }
        if ($end_time) {
            $map[] = ['a.add_time', '<', strtotime($end_time)];
        }
        if ($nameorphone) {
            $map[] = ['b.name|b.nickname|b.phone|c.title', 'like', '%' . $nameorphone . '%'];
        }

        try {
            $list  = Db::name('order a')
                ->join('__USERS__ b','a.user_id=b.user_id')
                ->where($map)
                ->field('a.*,b.level,b.name,b.nickname,b.total_recharge,b.avatar')
                ->order('a.add_time desc')
                ->select();

            $level = Db::name('user_level')->cache(true)->column('level_name', 'level_id');
        } catch (\Exception $e) {
            return json($e->getMessage(), 403);
        }
        return json([$list, $level]);
    }
}