<?php

namespace app\index\controller;

use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use think\facade\Cache;
use think\Db;
use think\Exception;

class Index extends Base
{
    public function index()
    {
        if (request()->isGet()) {
            $time      = time();
            $recommend = Db::name('item')->where("is_recommend=1 AND is_delete=0 AND on_sale=1")->field('detail', true)->order('sort desc')->select();
            $carousel  = Db::name('carousel')->where([['is_delete', '=', 0], ['start_time', '<', $time], ['end_time', '>', $time]])->order('sort desc')->field('id,link,img')->select();
            return $this->fetch('', ['recommend' => $recommend, 'carousel' => $carousel]);
        }

        $page   = input('page/d', 1);
        $size   = input('pagesize/d', 2);
        $result = Db::name('item')->where("is_delete=0 AND on_sale=1")->field('detail', true)->limit($size)->page($page)->select();
        if (!$result)
            return json('错误', 404);

        return json($result);
    }

    public function detail()
    {
        $id  = input('id', 0);
        $row = Db::name('item')->where("item_id={$id} AND on_sale=1 AND is_delete=0")->find();
        if (!$row)
            $this->error('您要查看的页面没找到哦!~');

        $row['img']         = Db::name('item_img')->where('item_id', $id)->order('sort desc')->select();
        $row['is_favorite'] = 0;
        if ($this->user_id) {
            $map                = [
                'user_id'   => $this->user_id,
                'type'      => 2,
                'link_id'   => $id,
                'is_delete' => 0
            ];
            $row['is_favorite'] = Db::name('user_behavior')->where($map)->count();
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    public function favorite()
    {
        $id   = input('post.item_id/d');
        $item = Db::name('item')->where('item_id', $id)->field('cate_id,cate_id2')->cache(true, 864000)->find();
        if (!$item) return json('参数错误');
        $row    = Db::name('user_behavior')->where(['user_id' => $this->user_id, 'type' => 2, 'link_id' => $id, 'is_delete' => 0])->find();
        $time   = time();
        $newRow = [
            'add_time' => $time,
            'link_id'  => $id,
            'cat_id1'  => $item['cate_id'],
            'cat_id2'  => $item['cate_id2'],
            'user_id'  => $this->user_id
        ];
        if ($row) {
            //取消收藏
            Db::name('user_behavior')->where('bid', $row['bid'])->update(['is_delete' => $time]);
            $newRow['type'] = 3;
            Db::name('user_behavior')->insert($newRow);
            return json();
        } else {
            $newRow['type'] = 2;
            Db::name('user_behavior')->insert($newRow);
            return json();
        }
    }

    public function qrcode()
    {
        $content = $this->request->get('content', request()->host());
        $qrcode  = new BaconQrCodeGenerator;
        $s       = $qrcode->format('png')->size(600)->backgroundColor(240, 255, 255)->margin(2)->merge('static/image/logo_2.png')->encoding('UTF-8')->generate($content);

        return response($s)->contentType('image/png');
    }

    public function couponInfo()
    {
        $idorcode = input('idorcode');
        $map      = [
            'a.is_delete' => 0,
            'b.is_delete' => 0
        ];
        if (is_numeric($idorcode))
            $map['a.id'] = $idorcode;
        else
            $map['a.code'] = $idorcode;
        $info = Db::name('coupon_list a')->join('coupon b', 'a.cid=b.id')->where($map)
            ->field('a.id,a.use_time,a.send_time,a.order_id,a.cid,b.money,b.condition,b.name,b.coupon_info,b.use_start_time,b.use_end_time')
            ->find();
        if ($info) return json($info);
        return json('优惠券不存在', 403);
    }

    public function prom()
    {
        $time = time();
        $map  = [
            ['id', '=', input('id')],
            ['is_delete', '=', 0],
            ['start_time', '<', $time],
            ['end_time', '>', $time]
        ];

        $prom = Db::name('promote')->where($map)->find();

        if (!$prom) $this->error('活动暂未开放');
        $type_btn    = ['领取优惠券', '参与活动', '立即转发', '立即打卡'];
        $prom['btn'] = $type_btn[$prom['type']];
        return $this->fetch('', ['prom' => $prom]);
    }

    //促销活动奖励
    public function reward()
    {
        $time = time();
        $map  = [
            ['id', '=', input('id')],
            ['is_delete', '=', 0],
            ['start_time', '<', $time],
            ['end_time', '>', $time],
            ['is_delete', '=', 0]
        ];

        $prom = Db::name('promote')->where($map)->find();

        if (!$prom) return json('活动不存在', 404);
        Db::name('promote')->where('id', $prom['id'])->setInc('view_count');
        switch ($prom['type']) {
            case 0:
                $this->sendCoupon($prom);
                break;
            case 3:
                $this->signIn($prom);
                break;
            default:
                return json('活动类型错误,请联系客服!', 403);
        }
    }

    private function sendCoupon($prom)
    {
        $time   = time();
        $map    = [
            ['id', '=', $prom['condition']],
            ['send_start_time', '<', $time],
            ['send_end_time', '>', $time],
            ['is_delete', '=', 0],
        ];
        $coupon = Db::name('coupon')->where($map)->find();
        if (!$coupon) exit(json('优惠券领取时间未开放!', 403)->send());
        $map   = [
            'user_id'   => $this->user_id,
            'cid'       => $coupon['id'],
            'is_delete' => 0
        ];
        $check = Db::name('coupon_list')->where($map)->count();
        if ($check) exit(json('您已经领过优惠券了哦,请在会员中心查看', 403)->send());
        if ($coupon['send_num'] >= $coupon['createnum']) exit(json('您来晚了,' . $coupon['createnum'] . '张优惠券已领取完毕!', 403)->send());
        try {
            //数据加锁、启用事务回滚,预防并发刷券;
            $count = 0;
            do {
                if (!Cache::connect(config('spec_cache'))->get('send_coupon_' . $coupon['id'])) break;
                usleep(200000);
                $count++;
                if ($count > 5) throw new Exception('并发超时,请稍后再试');
            } while (true);
            Cache::connect(config('spec_cache'))->set('send_coupon_' . $coupon['id'], true);
            Db::startTrans();
            Db::name('coupon')->where('id', $coupon['id'])->setInc('send_num');
            $row  = [
                'cid'       => $coupon['id'],
                'user_id'   => $this->user_id,
                'order_id'  => 0,
                'use_time'  => 0,
                'code'      => getCouponCode(),
                'send_time' => $time,
                'is_delete' => 0
            ];
            $flag = Db::name('coupon_list')->insertGetId($row);
            Db::commit();
            Cache::connect(config('spec_cache'))->rm('send_coupon_' . $coupon['id']);
            if ($flag) exit(json($coupon['money'] . '元优惠券领取成功!请到会员中心查看'));
            exit(json('优惠券领取失败', 403)->send());
        } catch (Exception $e) {
            Db::rollback();
            exit(json($e->getMessage(), 403)->send());
        }

    }

    private function signIn($prom)
    {
        $map   = [
            ['user_id', '=', $this->user_id],
            ['type', '=', 11],
            ['add_time', 'between', [$prom['start_time'], $prom['end_time']]]
        ];
        $count = Db::name('user_behavior')->where($map)->count();
        if ($count >= $prom['condition']) {
            exit(json('恭喜您完成持续打卡任务,不要忘记索取奖励', 403)->send());
        }
        $code = get_rand_str(6, 'sign_');//10位打卡随机字符串
        Cache::connect(config('spec_cache'))->set($code, $prom['id'] . '-' . $this->user_id, 3600);
        exit(json($code, 405)->send());
    }
}
