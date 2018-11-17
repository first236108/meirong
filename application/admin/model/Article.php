<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-11-17 下午2:08
 */

namespace app\admin\model;

use think\Model;

class Article extends Model
{
    protected $type = [
        'add_time' => 'timestamp',
    ];

    public function getTagAttr($value)
    {
        return explode(',', $value);
    }
}