<?php

namespace app\admin\model;

use think\Model;

class Users extends Model
{
    protected $pk = 'user_id';
    protected $type = [
        'birthday'        => 'timestamp',
        'create_time'     => 'timestamp',
        'last_come'       => 'timestamp',
        'last_login_time' => 'timestamp',
    ];

    //public function getLevelAttr($value)
    //{
    //    $level = [1 => '銀卡會員', 2 => '金卡會員', 3 => '星曜至尊會員'];
    //    return $level[$value];
    //}

    public function orders()
    {
        return $this->hasOne('Order');
    }

    public function getNameAttr($value, $data)
    {
        return $value ?$value: $data['nickname'];
    }

    public function getAvatarAttr($value, $data)
    {
        $sex_avatar = ['http://scsj-v2-bos.bj.bcebos.com/headImg/default.jpg', 'http://mallscsj.oss-cn-beijing.aliyuncs.com/upload/20180713190757_79241.jpeg'];
        $avatar     = $value ?? $sex_avatar[$data['sex']];
        return $avatar;
    }
}