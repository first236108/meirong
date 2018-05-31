<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/29 23:45
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\captcha\Captcha;

Class Login extends Controller
{
    public function index()
    {
        if (session('?admin'))
            $this->redirect(url('admin/index/index'));
        if ($cookie = cookie('admin')) {
            $map  = json_decode(deCrypt($cookie));
            $user = Db::name('admin')->where($map)->find();
            if ($user) {
                session('admin', $user);
                $this->redirect(url('admin/index/index'));
            } else
                cookie('admin', null);
        }

        if (request()->isPost()) {
            $data     = input('param.');
            $validate = new \app\admin\validate\Login();
            if (!$validate->check($data)) {
                return ['succ' => 1, 'msg' => $validate->getError()];
            }

            $user = Db::name('admin')->where('name', $data['username'])->find();
            if (!$user) {
                return ['succ' => 2, 'msg' => '用户不存在'];
            } elseif ($user['password'] != enPwd($data['password'], $user['salt'])) {
                return ['succ' => 3, 'msg' => '密码错误...'];
            } elseif ($user['status'] == 0) {
                return ['succ' => 4, 'msg' => '此用户已被禁止登录...'];
            } else {
                session('admin', $user);
                $data = [
                    'name'     => $user['name'],
                    'password' => $user['password'],
                ];
                cookie('admin', enCrypt(json_encode($data)), 86400 * 3);
                return ['succ' => 0, 'msg' => 'OK', 'url' => url('admin/index/index')];
            }
        }

        $config = getSiteConfig('site_info');
        $this->assign('config', $config);
        return view();
    }

    public function verify()
    {
        $code = input('code', 'login');
        return makcVerify($code);
    }
}