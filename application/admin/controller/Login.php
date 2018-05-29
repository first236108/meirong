<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/29 23:45
 */

namespace app\admin\controller;

use think\Controller;

Class Login extends Controller
{
    public function index()
    {
        if (request()->isPost()) {
            //todo
        }
        $config = getSiteConfig('site_info');
        $this->assign('config', $config);
        return view();
    }
}