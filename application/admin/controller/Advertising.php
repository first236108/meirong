<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 19-02-15 上午10:17
 */

namespace app\admin\controller;

use think\Db;

class Advertising extends Base
{
    public function carousel()
    {
        if ($this->request->isPost()) {
            $data   = input('post.');
            $result = $this->validate($data, [
                'name'       => 'require',
                'link'       => 'require',
                'start_time' => 'require',
                'end_time'   => 'require',
                'img'        => 'require',
            ], [
                'name'       => '轮播广告名称没填写哦',
                'link'       => '点击广告跳转的地址没填写哦',
                'start_time' => '需要选择轮播广告的开始时间',
                'end_time'   => '需要选择轮播广告的结束时间',
                'img'        => '图片没上传哦',
            ]);
            if (true !== $result) {
                return json($result, 403);
            }
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time']   = strtotime($data['end_time']);
            if ($data['id']) {
                $flag = Db::name('carousel')->update($data);
            } else {
                $data['sort'] = Db::name('carousel')->where('is_delete', 0)->max('sort') + 1;
                $flag         = Db::name('carousel')->insert($data);
            }
            if ($flag) return json();
            else return json('保存失败', 403);
        }
        if ($this->request->isAjax()) {
            if (input('action') == 'sort' && input('ids')) {
                $ids   = explode(',', input('ids'));
                $count = count($ids);
                $sql   = "UPDATE " . config('database.prefix') . "carousel SET `sort` = CASE `id` ";
                foreach ($ids as $id) {
                    $sql .= "WHEN $id THEN $count ";
                    $count--;
                }
                $sql .= "END";
                Db::execute($sql);
                return json();
            }
            if (input('action') == 'del' && input('id')) {
                Db::name('carousel')->where('id', input('id'))->setField('is_delete', time());
                return json();
            }
            $list = Db::name('carousel')->where('is_delete', 0)->order('sort desc')->select();
            return json($list);
        }

        return $this->fetch();
    }

    public function coupon()
    {
        if ($this->request->isPost()) {
            $data    = input('post.');
            $timestr = date('Y-m-d');
            $result  = $this->validate($data, [
                'name'            => 'require',
                'type'            => 'require',
                'coupon_info'     => 'require',
                'money'           => 'require|gt:0',
                'condition'       => 'require',
                'createnum'       => 'require|gt:0',
                'send_start_time' => 'require',
                'send_end_time'   => 'require|after:' . $timestr,
                'use_start_time'  => 'require',
                'use_end_time'    => 'require|after:' . $timestr,
            ], [
                'name'            => '名称没填写哦',
                'coupon_info'     => '优惠券描述未填写',
                'type'            => '优惠券类型未选择',
                'money'           => '面额未填写',
                'condition'       => '使用条件（金额）未填写',
                'createnum'       => '发行数量未填写',
                'send_start_time' => '优惠券发放/领取期限未选择',
                'send_end_time'   => '优惠券发放/领取期限未选择',
                'use_start_time'  => '优惠券使用期限未选择',
                'use_end_time'    => '优惠券使用期限未选择',
            ]);
            if (true !== $result) {
                return json($result, 403);
            }
            $data['send_start_time'] = strtotime($data['send_start_time']);
            $data['send_end_time']   = strtotime($data['send_end_time']);
            $data['use_start_time']  = strtotime($data['use_start_time']);
            $data['use_end_time']    = strtotime($data['use_end_time']);
            if ($data['id']) {
                $flag = Db::name('coupon')->update($data);
            } else {
                $flag = Db::name('coupon')->insert($data);
            }
            if ($flag) return json();
            else return json('保存失败', 403);
        }
        if ($this->request->isAjax()) {
            if (input('action') == 'detail') {
                $coupon_list = Db::name('coupon_list a')
                    ->join('users b', 'a.user_id=b.user_id')
                    ->where(['a.cid' => input('id'), 'a.is_delete' => 0])
                    ->field('a.*,b.user_id,b.name,b.nickname')
                    ->order('a.id desc')
                    ->select();
                return json($coupon_list);
            }
            if (input('action') == 'send') {
                //发放优惠券
                $id      = input('id/d', 0);
                $user_id = input('user_id/d', 0);
                if (!$id || !$user_id) return json('参数错误', 403);
                $coupon = Db::name('coupon')->where('id', $id)->find();
                if (!$coupon) return json('优惠券不存在', 403);
                if ($coupon['send_num'] >= $coupon['createnum']) return json('【' . $coupon['name'] . '】发放数量已满', 403);
                if ($coupon['type'] == 1) {
                    if (Db::name('coupon_list')->where(['user_id' => $user_id, 'is_delete' => 0])->count()) return json('免费领取类型的优惠券，每人仅可领取一张', 403);
                }
                $row       = [
                    'cid'       => $coupon['id'],
                    'user_id'   => $user_id,
                    'order_id'  => 0,
                    'use_time'  => 0,
                    'send_time' => time(),
                    'code'      => getCouponCode()
                ];
                $row['id'] = Db::name('coupon_list')->insertGetId($row);
                Db::name('coupon')->where('id', $id)->setInc('send_num');
                $row['coupon_info'] = $coupon['coupon_info'];
                $row['money']       = $coupon['money'];
                $row['order_id']    = 0;
                return json($row);
            }
            $list = Db::name('coupon')->where('is_delete', 0)->order('id desc')->select();
            return json(['list' => $list, 'types' => ['面额模板', '免费领取']]);
        }
        return $this->fetch();
    }

    public function salepage()
    {
        if ($this->request->isPost()) {
            if (input('post.action') == 'del') {
                $id = input('post.id');
                Db::name('promote')->where('id', $id)->update(['is_delete' => time()]);
                return json();
            }

            $data   = input('post.');
            $result = $this->validate($data, [
                'title'      => 'require',
                'type'       => 'require',
                'start_time' => 'require|length:10',
                'end_time'   => 'require|length:10',
                'content'    => 'require',
            ], [
                'title'      => '活动标题必填',
                'type'       => '请选择活动类型',
                'start_time' => '请选择活动开始时间',
                'end_time'   => '请选择活动结束时间',
                'content'    => '请填写活动页面内容',
            ]);
            if (true !== $result) {
                return json($result, 403);
            }
            $data['add_time'] = time();
            if (isset($data['id']))
                $flag = Db::name('promote')->update($data);
            else
                $flag = Db::name('promote')->insert($data);
            if ($flag) return json();
            return json('保存失败', 403);
        }

        if ($id = input('get.id/d', 0)) {
            $salepage = Db::name('promote')->where('id', $id)->find();
            if ($salepage) return json($salepage);
            return json('活动页面不存在', 403);
        }
        $map = 'is_delete=0';
        if (input('get.is_delete')) $map = 'is_delete>0';
        $list = Db::name('promote')->where($map)->select();
        $type = ['优惠券领取', '订单/项目类优惠', '分享转发/到店打卡奖励'];
        return $this->fetch('', ['list' => $list, 'type' => $type]);
    }
}