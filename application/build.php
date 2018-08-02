<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/28 22:35
 */
return [
    // 定义test模块的自动生成
    'admin' => [
        '__dir__'    => ['controller', 'model', 'view'],
        'controller' => ['User', 'UserType'],
        'model'      => ['User', 'UserType'],
        'view'       => ['index/index', 'index/test'],
    ],
];