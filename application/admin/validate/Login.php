<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/31 17:48
 */

namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'username'    => 'require',
        'password'    => 'require|min:6',
        'error_count' => 'require|check_count:0'
    ];
    protected $message = [
        'username.require'        => '登录名称必须填写呀',
        'password.require'        => '密码怎么能省呢...',
        'password.min'            => '密码长度好像不够哦...',
        'error_count.require'     => '请刷新后再试',
        'error_count.check_count' => '验证码错误',
    ];

    protected function check_count($value, $rule, $data = [])
    {
        if ($value > $rule)
            return true;
        if (!$data['verify'])
            return '验证码必须填写';
        if (checkVerify($data['verify'], 'login')=== true)
            return true;
        return '验证码错误...';
    }
}