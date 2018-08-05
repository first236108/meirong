<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/6/10 0:25
 */

namespace app\admin\controller;

use think\Db;

class Item extends Base
{
    public function Index()
    {
        $type=input('type',0);
        $category = Db::name('item_cate')->column('name', 'cate_id');
        $map      = ['is_delete' => $type];
        $count    = Db::name('item')->where($map)->count();
        $list     = Db::name('item')
            ->where($map)->field('detail', true)->paginate(16, $count)->each(function ($item, $key) use ($category) {
                $item['cate_name'] = $category[$item['cate_id']] . ' ' . $category[$item['cate_id2']];
                return $item;
            });
        $this->assign('page', $list->render());
        $this->assign('list', $list);
        $this->assign('type', $type);
        return view();
    }

    public function addEditItem()
    {
        $id = input('item_id', 0);
        if (request()->isPost()) {
            $images = input('image/a', []);
            $sort   = input('sort/a', []);
            $data   = input('param.');
            unset($data['sort'], $data['image']);
            $result = $this->validate($data, [
                'title'         => 'require',
                'price'         => 'require|gt:0',
                'market_price'  => 'require|gt:price',
                'give_integral' => 'number|integer',
                'cate_id'       => 'require|number|integer',
                'cate_id2'      => 'require|number|integer',
                'description'   => 'require',
                'origin_image'  => 'require',
            ], [
                'title'         => '服务项目名称必须填写',
                'price'         => '价格必须是大于零的数字',
                'market_price'  => '原价最好大于价格哦',
                'give_integral' => '赠送积分必须是整数',
                'cate_id'       => '一级分类错误',
                'cate_id2'      => '二级分类错误',
                'description'   => '请填写简介描述',
                'origin_image'  => '主图格式错误',
            ]);
            if (true !== $result)
                return json(['succ' => 1, 'msg' => $result]);

            $item           = new \app\admin\model\Item();
            $data['detail'] = $data['content'];
            if ($id)
                $flag = $item->allowField(true)->isUpdate(true)->save($data);
            else {
                $flag = $item->allowField(true)->save($data);
                $id   = $item->item_id;
            }

            #插入/更新 项目轮播图
            if (count($images)) {
                foreach ($images as $index => $image) {
                    if (!$image)
                        continue;
                    $img[$index]['item_id'] = $id;
                    $img[$index]['image']   = $image;
                    $img[$index]['sort']    = $sort[$index];
                }
                if (count($img)) {
                    Db::name('item_img')->where('item_id', $id)->delete();
                    Db::name('item_img')->insertAll($img);
                }
            }

            return json(['succ' => !$flag]);
        }
        $info = Db::name('item')->where('item_id', $id)->find();
        $info['img']  = Db::name('item_img')->where('item_id', $id)->select();
        return json($info);
    }

    public function deleteItem()
    {
        $ids = input('ids', '');
        if (!$ids)
            return ['succ' => 1, 'msg' => '参数错误'];
        $flag        = Db::name('item')->whereIn('item_id', $ids)->setField('is_delete', 1);
        $res['succ'] = $flag ? 0 : 1;
        $res['msg']  = $res['succ'] ? '操作失败' : '操作完成';
        return $res;
    }

    public function getCate()
    {
        $first  = Db::name('item_cate')->where('level=1')->order('sort desc')->column('name', 'cate_id');
        $second = Db::name('item_cate')->where('level=2')->order('sort desc')->column('name', 'cate_id');
        if ($first && $second)
            return ['succ' => 0, 'data' => ['cate1' => $first, 'cate2' => $second]];
        return ['succ' => 1, 'msg' => '数据异常'];
    }

    public function categoryList()
    {
        if ($this->request->isPost()) {
            $first  = Db::name('item_cate')->where('level', 1)->select();
            $second = Db::name('item_cate')->where('level', 2)->select();
            $list   = $first;
            foreach ($first as $index => $item) {
                foreach ($second as $k => $v) {
                    if ($item['cate_id'] == $v['parent_id'])
                        $list[$index]['children'][] = $v;
                }
            }
            return ['succ' => 0, 'data' => ['first' => $first, 'list' => $list]];
        }

        return view();
    }

    public function addEditCate()
    {
        $id = input('cate_id/d', 0);
        if ($this->request->isPost()) {
            $name      = input('name', '');
            $parent_id = input('parent/d', 0);
            if (!$name)
                return ['succ' => 1, 'msg' => '分类名称不能为空'];
            if ($id) {
                $data = input('post.');
                $flag = Db::name('item_cate')->update($data);
            } else {
                $filepath = input('image', '');
                //            if (!$filepath)
                //                return ['succ' => 1, 'msg' => '请上传分类图标...'];

                $data = [
                    'name'      => $name,
                    'level'     => $parent_id ? 2 : 1,
                    'parent_id' => $parent_id,
                    'sort'      => 0,
                    'image'     => $filepath
                ];
                $flag = Db::name('item_cate')->insertGetId($data);
            }

            $res['succ'] = $flag ? 0 : 1;
            $res['msg']  = $res['succ'] ? '操作失败' : '操作完成';
            return $res;
        }

        $info = Db::name('item_cate')->find($id);
        return ['succ' => 0, 'info' => $info];
    }

    public function categoryDel()
    {
        $id   = input('post.id/d', 0);
        $flag = Db::name('item_cate')->where('cate_id', $id)->delete();
        return ['succ' => !$flag, 'msg' => $flag ? '已删除' : '删除失败'];
    }
}