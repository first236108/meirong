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
        $this->authControl();
        $this->publicAssign();
    }

    //验证当前访问权限
    public function authControl()
    {
        $a = $this->rbacAuth->check(request()->module() . '/' . request()->controller() . '/' . request()->action(), session('admin.id'));
        if (!$a) {
            $this->error('权限不足!');
        }
    }

    //获取权限title数组
    public function authChk($uid)
    {
        $rules = Db::name('auth_rule')->cache(true)->select();
        $arr   = [];
        foreach ($rules as $key => $value) {
            $arr[$value['title']] = $this->rbacAuth->check($value['name'], $uid);
        }
        return $arr;
    }

    //面包屑导航
    public function publicAssign()
    {
        $rules                  = Db::name('auth_rule')->cache(true)->select();
        $second                 = request()->module() . '/' . request()->controller() . '/' . 'index';
        $third                  = request()->module() . '/' . request()->controller() . '/' . request()->action();
        $second_key             = array_search(strtolower($second), array_column($rules, 'name'), true);
        $third_key              = array_search(strtolower($third), array_column($rules, 'name'), true);
        $breadcrumb[0]['title'] = 'Home';
        $breadcrumb[0]['link']  = url('admin/index/index');
        $breadcrumb[1]['title'] = $rules[$second_key]['title'];
        $breadcrumb[1]['link']  = url($second);
        $breadcrumb[2]['title'] = $rules[$third_key]['title'];
        $breadcrumb[2]['link']  = url($third);
        $this->assign('breadcrumb', $breadcrumb);
        $config = getSiteConfig('site_info');
        $this->assign('config', $config);
    }
}