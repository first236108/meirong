<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\captcha\Captcha;
use think\Db;

// 应用公共文件
function getSiteConfig($type = '')
{
    $list = Db::name('config')->where('type', $type)->column('value', 'name');
    return $list;
}

/**
 * 生成验证码
 * @param $code string 验证码id
 * @param array $option 配置
 * @return \think\Response 图片
 */
function makcVerify($code, $option = [])
{
    $default = [
        'expire'   => 300,
        'fontSize' => 25,
        'useCurve' => false,
        'length'   => 4
    ];
    $option  = array_merge($default, $option);
    $captcha = new Captcha($option);
    return $captcha->entry($code);
}

/**
 * 检查验证码
 * @param $value string
 * @param $code string 验证码id
 * @return bool 验证结果
 */
function checkVerify($value, $code = 'login')
{
    $captcha = new Captcha();
    if (!$captcha->check($value, $code)) {
        return false;
    }
    return true;
}

/**
 * 生成随机字符串
 * @param int $length 长度
 * @return string 随机字符串
 */
function get_rand_str($length = 8)
{
    $str    = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max    = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

/**
 * 可逆的字符串加密函数
 * @param string $txtStream 待加密的字符串内容
 * @param string $password 加密密码
 * @return string 加密后的字符串
 */
function enCrypt($txtStream, $password = 'ENCRYPT_KEY')
{
    //密锁串，不能出现重复字符，内有A-Z,a-z,0-9,/,=,+,_,
    $lockstream = 'st=lDEFABCNOPyzghi_jQRST-UwxkVWXYZabcdef+IJK6/7nopqr89LMmGH012345uv';
    //随机找一个数字，并从密锁串中找到一个密锁值
    $lockLen    = strlen($lockstream);
    $lockCount  = rand(0, $lockLen - 1);
    $randomLock = $lockstream[$lockCount];
    //结合随机密锁值生成MD5后的密码
    $password = md5($password . $randomLock);
    //开始对字符串加密
    $txtStream = base64_encode($txtStream);
    $tmpStream = '';
    $k         = 0;
    for ($i = 0; $i < strlen($txtStream); $i++) {
        $k         = ($k == strlen($password)) ? 0 : $k;
        $j         = (strpos($lockstream, $txtStream[$i]) + $lockCount + ord($password[$k])) % ($lockLen);
        $tmpStream .= $lockstream[$j];
        $k++;
    }
    return $tmpStream . $randomLock;
}

/**
 * 可逆的字符串解密函数
 * @param int $txtStream 待加密的字符串内容
 * @param string $password 解密密码
 * @return string 解密后的字符串
 */
function deCrypt($txtStream, $password = 'ENCRYPT_KEY')
{
    //密锁串，不能出现重复字符，内有A-Z,a-z,0-9,/,=,+,_,
    $lockstream = 'st=lDEFABCNOPyzghi_jQRST-UwxkVWXYZabcdef+IJK6/7nopqr89LMmGH012345uv';

    $lockLen = strlen($lockstream);
    //获得字符串长度
    $txtLen = strlen($txtStream);
    //截取随机密锁值
    $randomLock = $txtStream[$txtLen - 1];
    //获得随机密码值的位置
    $lockCount = strpos($lockstream, $randomLock);
    //结合随机密锁值生成MD5后的密码
    $password = md5($password . $randomLock);
    //开始对字符串解密
    $txtStream = substr($txtStream, 0, $txtLen - 1);
    $tmpStream = '';
    $k         = 0;
    for ($i = 0; $i < strlen($txtStream); $i++) {
        $k = ($k == strlen($password)) ? 0 : $k;
        $j = strpos($lockstream, $txtStream[$i]) - $lockCount - ord($password[$k]);
        while ($j < 0) {
            $j = $j + ($lockLen);
        }
        $tmpStream .= $lockstream[$j];
        $k++;
    }
    return base64_decode($tmpStream);
}

/**
 * 后台登录密码加密
 * @param $pwd
 * @param $salt
 * @return bool|string
 */
function enPwd($pwd, $salt)
{
    if (strlen($salt))
        return md5(substr($salt, 0, 4) . $pwd . substr($salt, -4));
    else
        return false;
}