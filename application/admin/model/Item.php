<?php
namespace app\admin\model;

use think\Model;

class Item extends Model
{
    protected $pk = 'item_id';
    public function getContentAttr($value,$data)
    {
        return $data['detail'];
    }
}