<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/29 23:45
 */

namespace app\admin\controller;

use think\Controller;
use think\captcha\Captcha;

Class Login extends Controller
{
    public function index()
    {
        if (request()->isPost()) {
            dump(input('param.'));
        }
        $config = getSiteConfig('site_info');
        $this->assign('config', $config);
        return view();
    }

    public function verify()
    {
        $code    = input('code', 'login');
        $captcha = new Captcha();
        return $captcha->entry($code);
    }

    public function check($value, $code)
    {
        $captcha = new Captcha();
        if (!$captcha->check($value, $code)) {
            return false;
        }
        return true;
    }
}