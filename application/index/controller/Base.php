<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-12-16 下午7:07
 */

namespace app\index\controller;

use think\Controller;
use think\facade\Cache;

class Base extends Controller
{
    public $token = '';
    public $user_id = 0;
    public $user = [];

    public function initialize()
    {
        $this->token = $this->request->header('token');
        if (!$this->token)
            $this->token = cookie('token');
        $this->user = Cache::get($this->token);
        if ($this->user)
            $this->user_id = $this->user['user_id'];
        $needLogin  = [
            'index'   => ['favorite'],
            'article' => [],
            'user'    => ['message', 'userinfo', 'modifypwd', 'order', 'appointment', 'ajaxappointment',
                'comment', 'wallet', 'coupon', 'photoshow', 'favorite', 'advisory'
            ]
        ];
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        if (in_array($action, $needLogin[$controller]) && !$this->user_id) {
            if ($this->request->isAjax()) {
                json(['msg' => '请登录后操作'], 401)->send();
            } else {
                $this->redirect('user/login', ['refer' => $controller . '-' . $action]);
            }
        }

        $this->assign('user_id', $this->user_id);
    }
}