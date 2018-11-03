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
    $list = Db::name('config')->where('type', $type)->cache(true)->column('value', 'name');
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
 * @param string $prefix 前缀
 * @return string 随机字符串
 */
function get_rand_str($length = 8, $prefix = '')
{
    $str    = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max    = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    $prefix && $str = $prefix . substr($str, 1);
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

/**
 * 获取中文汉字拼音首字母
 * @param $str
 * @return null|string
 */
function getFirstCharter($str)
{
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
    $s1  = iconv('UTF-8', 'gb2312', $str);
    $s2  = iconv('gb2312', 'UTF-8', $s1);
    $s   = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return null;
}

/**
 * 生成唯一订单号18位
 * @return string
 */
function getNewOrderSn()
{
    while (true) {
        list($usec, $sec) = explode(" ", microtime());
        $sn = date('Ymd') . substr($sec, 5) . 1000 * $usec . mt_rand(1000, 9999);
        if (Db::name('order')->where('order_sn', $sn)->count())
            continue;
        break;
    }
    return $sn;
}

/**
 * 记录订单日志
 * @param $order_id
 * @param $operater
 * @param $msg
 */
function write_order_log($order_id, $operater, $msg)
{
    $data = [
        'order_id' => $order_id,
        'operater' => $operater,
        'msg'      => $msg,
        'log_time' => time()
    ];
    Db::name('order_action')->insert($data);
}

/**
 * 会员行为记录
 * @param $type
 * @param int $user_id
 * @param int $link_id
 * @param string $session_id
 */
function user_log($type, $user_id = 0, $link_id = 0, $session_id = '')
{

    $data = [
        'user_id'  => $user_id,
        'type'     => behaviorType($type),
        'link_id'  => $link_id,
        'add_time' => time()
    ];

    if ($data['type'] == 1) {
        $item_info       = Db::name('item')->cache(true, 864000)->where('item_id', $link_id)->field('cate_id,cate_id2')->find();
        $data['cat_id1'] = $item_info['cate_id'];
        $data['cat_id2'] = $item_info['cate_id2'];
    }
    if ($data['user_id'] == 0) {
        $data['session_id'] = $session_id;
    }
    Db::name('user_behavior')->insert($data);
}

/**
 * 获取用户行为type，或行为注释数组
 * @param $key
 * @param int $value
 * @param boolean $get_comment
 * @return string|int|array
 */
function behaviorType($key, $value = null, $get_comment = false)
{
    $type = [
        'login'       => 0,//登录
        'visit'       => 1,//浏览商品--表名item
        'faverite'    => 2,//收藏--表名item
        'hate'        => 3,//取消收藏--表名item
        'order'       => 4,//充值|购买--表名order
        'advice'      => 5,//咨询--表名advice
        'appointment' => 6,//预约--表名consumption
        'cancel'      => 7,//cancel预约--表名consumption
        'consumption' => 8,//消费--表名consumption
        'share'       => 9,//分享--表名share
        'activity'    => 10,//参加活动--表名activity
        'check_in'    => 11,//到店打卡
    ];
    if (!is_null($value))
        return array_flip($type)[$value];
    if ($get_comment)
        return [
            0  => '登录',
            1  => '浏览商品',
            2  => '收藏',
            3  => '取消收藏',
            4  => '充值|购买',
            5  => '咨询',
            6  => '预约',
            7  => '取消预约',
            8  => '消费',
            9  => '分享',
            10 => '参加活动',
            11 => '到店打卡',
        ];
    return $type[$key];
}

/**
 * 预约
 * @param $order_item
 * @param int $user_id
 * @param null $time
 * @return array|bool
 */
function apponintment($order_item, $user_id = 0, $time = null)
{
    if ($user_id == 0) {
        $user_id = Db::name('order')->cache(true)->where('order_id', $order_item['order_id'])->value('user_id');
    }
    $now = time();
    $row = [
        'user_id'    => $user_id,
        'order_id'   => $order_item['order_id'],
        'item_id'    => $order_item['id'],
        'add_time'   => $now,
        'schedule'   => $time ?? $now,
        'qrcode'     => uniqueCode(),
        'confirm_id' => 0,
        'is_delete'  => 0,
    ];
    $cid = Db::name('consumption')->insertGetId($row);
    if ($cid) {
        $row['cid'] = $cid;
        return $row;
    }
    return false;
}

/**
 * 生成唯一预约码 12位数字,年月4位 秒数5位 随机3位
 * @return string
 */
function uniqueCode()
{
    while (true) {
        list($usec, $sec) = explode(" ", microtime());
        $sn = date('md') . substr($sec, 5) . mt_rand(100, 999);
        if (Db::name('consumption')->where('qrcode', $sn)->count())
            continue;
        break;
    }
    return $sn;
}

function defaultAvatar($arr){
    $sex_avatar = ['http://scsj-v2-bos.bj.bcebos.com/headImg/default.jpg', 'http://mallscsj.oss-cn-beijing.aliyuncs.com/upload/20180713190757_79241.jpeg'];
    foreach ($arr as $index => $item) {
        $arr[$index]['avatar'] = $item['avatar'] ?? $sex_avatar[$item['sex']];
    }
    return $arr;
}