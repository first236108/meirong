<?php

namespace app\index\controller;

use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use think\Db;

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
        $item = Db::name('item')->cache(true, 864000)->where('item_id', $id)->field('cate_id,cate_id2')->find();
        if (!$item) return json(['msg' => '参数错误']);
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
        dump(input('id'));die;
    }
}
