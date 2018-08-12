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

    public function user()
    {
        return $this->belongsTo('User');
    }
}