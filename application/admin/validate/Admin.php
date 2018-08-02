<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/3 22:59
 */

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'name'=>'require|unique:admin',
        'nickname'=>'require',
        'group_id'=>'require|gt:0'
    ];
    protected $message = [
        'name.require'=>'登录账号总得写一个呀',
        'name.unique'=>'登录账号已存在，换一个试试？',
        'nickname'=>'昵称要填写哦',
        'group_id'=>'请选择所属权限'
    ];
}