<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/3 22:59
 */

namespace app\admin\validate;

use think\Validate;

class Item extends Validate
{
    protected $rule = [
        'user_id'        => 'require|gt:0',
        'phone'          => 'mobile',
        //'total_amount'   => 'require|gt:0',//FIXME 注释掉，支持充值余额
        'use_money'      => 'gt:0',
        'use_points'     => 'gt:0',
        'manager_reduce' => 'gt:0',
        'pay_amount'     => 'gt:0',
        'confirm_id'     => 'require|gt:0'
    ];
    protected $message = [
        'user_id'        => '会员信息错误',
        'phone'          => '会员手机号码错误',
        'total_amount'   => '订单金额错误',
        'use_money'      => '使用余额错误',
        'use_points'     => '使用积分错误',
        'manager_reduce' => '店长调价金额错误',
        'pay_amount'     => '实际支付金额错误',
        'confirm_id'     => '请选择跟单美容师'
    ];
}