<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/29 23:32
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use rbac\Auth;

class Base extends Controller
{
    protected $rbacAuth;

    public function initialize()
    {
        if (!session('?admin')) {
            if (!cookie('admin')) {
                $this->redirect('admin/login/index');
            } else {
                $map  = json_decode(deCrypt(cookie('admin')));
                $user = Db::name('admin')->where($map)->find();
                if ($user)
                    session('admin', $user);
                else
                    $this->redirect('admin/login/index');
            }
        }

        #自动登录EOL，权限验证开始
        $this->rbacAuth = new Auth();
        $uid            = session('admin.id');
        if (!session('?adminGroup')) {
            $adminGroup = $this->rbacAuth->getGroups($uid);
            session('adminGroup', $adminGroup[0]['title']);
        }
        if (!session('?auth')) {
            $arr = $this->authChk($uid);
            foreach ($arr as $index => $item) {
                $auth[$index] = $item;
            }
            isset($auth) && session('auth', $auth);
        }
        $this->auth_control();
    }

    public function auth_control()
    {
        $a = $this->rbacAuth->check(request()->module() . '/' . request()->controller(), session('admin.id'));
        if (!$a) {
            $a = $this->rbacAuth->check(request()->module() . '/' . request()->controller() . '/' . request()->action(), session('admin.id'));
            if (!$a) {
                $this->error('权限不足!');
            }
        }
    }

    public function authChk($uid)
    {
        $rules = Db::name('auth_rule')->select();
        $arr   = [];
        foreach ($rules as $key => $value) {
            $arr[$value['title']] = $this->rbacAuth->check($value['name'], $uid);
        }
        return $arr;
    }

}