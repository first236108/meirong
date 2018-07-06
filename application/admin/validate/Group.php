<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/6 23:33
 */

namespace app\admin\validate;

use think\validate;

class Group extends validate
{
    protected $rule = [
        'title' => 'require|unique:auth_group',
        'rules' => 'require|array'
    ];
    protected $message = [
        'title.require' => '角色名称总得写呀...',
        'title.unique'  => '角色名称已经存在，换一个试试？',
        'rules.require' => '请至少分配一项权限',
        'rules.array'   => '非法数据',
    ];

}