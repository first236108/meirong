<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 19-1-5 下午10:02
 */

use think\Db;
use think\facade\Cache;

function flashToken($token, $field, $val)
{
    $user         = Cache::get($token);
    $user[$field] = $val;
    Cache::set($token, $user);
}