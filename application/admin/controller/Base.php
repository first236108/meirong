<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 2018/5/29 23:32
 */
namespace app\admin\controller;
use think\Controller;
use rbac\Auth;
class Base extends Controller
{
    public function initialize(){
        if (!isset($_SESSION['user'])) {
            if (!cookie('id')) {
                $this->redirect('Login/index');
            }else{
                $_SESSION['user']=array(
                    'id'=>cookie('id'),
                    'name'=>cookie('name'),
                    'nickname'=>cookie('nickname'),
                    'photo'=>cookie('photo')
                );
            }
        }
        $uid=$_SESSION['user']['id'];
        if (!$_SESSION['adminGroup']){
            $auth=new Auth();
            $adminGroup=$auth->getGroups($uid);
            $_SESSION['adminGroup']=$adminGroup[0]['title'];
        }
        if (!isset($_SESSION['auth'])){
            $arr=$this->authChk($uid);
            foreach ($arr as $index => $item) {
                $_SESSION['auth'][$index]=$item;
            }
            unset($index,$item);
        }
        $this->auth_control();
    }

    public function auth_control()
    {
        $auth=new Auth();
        $a=$auth->check(request()->module().'/'.request()->controller(),$_SESSION['user']['id']);
        if (!$a){
            $a=$auth->check(request()->module().'/'.request()->controller().'/'.request()->action(),$_SESSION['user']['id']);
            if (!$a){
                $this->error('权限不足!');
            }
        }
    }

    public function authChk($uid){
        $auth=new Auth();
        $rules=db('auth_rule')->select();
        $arr=array();
        foreach ($rules as $key => $value) {
            $arr[$value['title']]=$auth->check($value['name'],$uid);
        }
        return $arr;
    }

    public function getSalt($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];
        }
        return $str;
    }

    public function delete_img($images)
    {
        $arr=explode(',',$images);
        foreach ($arr as $key => $value){
            unlink('.'.$value);
        }
    }
}