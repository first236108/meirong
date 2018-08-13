<?php

namespace app\admin\model;

use think\Model;

class Orders extends Model
{
    protected $name = 'order';
    protected $pk = 'order_id';
    protected $type = [
        'add_time' => 'timestamp',
        'pay_time' => 'timestamp',
    ];

    public function users()
    {
        return $this->belongsTo('Users', 'user_id');
    }

    public function getStatusAttr($value)
    {
        $text = [
            '0' => '待支付',
            '1' => '已付款',
            '2' => '已退款',
            '3' => '已作废',
            '4' => '已过期'
        ];
        return $text[$value];
    }
}