<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-12-16 上午11:14
 */

namespace app\index\controller;

use think\Db;

class Article extends Base
{
    public function index()
    {
        if ($this->request->isGet())
            return $this->fetch();

        $p                = input('page/d', 1);
        $map['is_delete'] = 0;

        $list = Db::name('article a')
            ->join('article_commit b', 'a.id=b.article_id', 'LEFT')
            ->where($map)->page($p, 4)->field('a.*,b.type as commit_type,b.user_id')->order('a.id desc')->select();
        if (!$list)
            return json(['msg' => '没有更多了'], 403);

        $tags = [];
        foreach ($list as $index => $item) {
            $temp                = explode(',', $item['tag']);
            $list[$index]['tag'] = $temp;
            $tags                = array_merge($tags, $temp);
        }
        $style = ['primary', 'warning', '', 'danger', 'hollow'];
        foreach (array_unique($tags) as $index => $item) {
            if ($index > 3)
                $index = $index % 4;
            $tag_style[$item] = $style[$index];
        }

        $this->assign('list', $list);
        $this->assign('tag_style', $tag_style ?? []);
        return $this->fetch('ajax_index');
    }

    /**
     * 文章点赞
     * @return \think\response\Json
     */
    public function star()
    {
        $id = input('post.id', 0);
        if (!$this->user_id)
            return json(['msg' => '请登录后操作'], 401);
        $count = Db::name('article_commit')->where("article_id={$id} AND user_id = {$this->user_id}")->count();
        if (!$count)
            Db::name('article_commit')->insert([
                'article_id' => $id,
                'user_id'    => $this->user_id,
                'type'       => 0,
                'add_time'   => time()
            ]);
        return json();
    }

    /**
     * 视频点击数增加
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function video_click()
    {
        $id = input('post.id/d');
        Db::name('article')->where("id={$id} AND `type`=1")->setInc('click_count');
        return json();
    }

    /**
     * 文章详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $id      = input('id/d', 0);
        $article = Db::name('article')->where("is_delete=0 AND `id`={$id}")->find();
        if (!$article)
            $this->error('您要查看的页面已经不存在啦!');
        $commit = Db::name('article_commit a')
            ->join('users b', 'a.user_id=b.user_id')
            ->where("a.article_id={$id} AND `a`.`type`=1")
            ->field('a.*,b.nickname,b.avatar')
            ->select();
        $this->assign('commit', $commit);
        $this->assign('article', $article);
        return $this->fetch();
    }
}