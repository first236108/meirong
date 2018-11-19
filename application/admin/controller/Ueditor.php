<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-11-11 上午10:02
 */

namespace app\admin\controller;

use think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Exception;

class Ueditor extends Controller
{
    private $config;

    public function initialize()
    {
        $this->config                   = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(env('config_path') . "config.json")), true);
        $this->config['imageFieldName'] = 'file';
        $this->config['imageUrl']       = 'https://upload-z1.qiniup.com';
        $this->config['imageUrlPrefix'] = config('qiniu_cdn');

        if (input('action', '') == 'config') {
            json($this->config)->send();
            exit;
        }
    }

    public function index()
    {
        $action = input('action');
        switch ($action) {
            /* 抓取远程图片 */
            case 'catchimage':
                $result = $this->cacheRemote();
                break;
            default:
                $result = json_encode(['state' => '请求地址出错']);
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(['state' => 'callback参数不合法']);
            }
        } else {
            echo $result;
        }
    }

    private function cacheRemote()
    {
        header("Content-Type: text/html; charset=utf-8");
        //远程抓取图片配置
        $config = [
            "savePath"   => env('root_path') . 'public/cacheRemote/' . date('Y') . '/' . date('m') . '/', //保存路径
            "allowFiles" => ["gif", "png", "jpg", "jpeg", "bmp"], //文件允许格式
            "maxSize"    => 20000000,
        ];
        $images = $this->request->param($this->config['catcherFieldName'], []);

        //忽略抓取时间限制
        set_time_limit(0);
        //ue_separate_ue  ue用于传递数据分割符号
        $list = [];
        foreach ($images as $imgUrl) {
            //http开头验证
            if (strpos($imgUrl, "http") !== 0) {
                array_push($list, "https error");
                continue;
            }
            //获取请求头，遇到https路径，需要自己配置好本机PHP开启SSL并设置ca证书
            $heads = get_headers($imgUrl);
            //死链检测
            if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
                array_push($list, "get_headers error");
                continue;
            }

            foreach ($heads as $index => $head) {
                if (strpos($head, 'Content-Type') !== false) {
                    $content_type_check = strtolower(stristr($head, "image"));
                    break;
                }
            }

            //格式验证(扩展名验证和Content-Type验证)
            $fileType = isset($content_type_check) ? ltrim($content_type_check, 'image/') : null;

            if (!in_array($fileType, $config['allowFiles']) || !isset($content_type_check)) {
                array_push($list, "Content-Type error");
                continue;
            }

            //打开输出缓冲区并获取远程图片
            ob_start();
            $context = stream_context_create(['http' => ['follow_location' => false]]); // don't follow redirects

            //请确保php.ini中的fopen wrappers已经激活，即allow_url_fopen = On
            readfile($imgUrl, false, $context);
            $img = ob_get_contents();
            ob_end_clean();
            $uriSize   = strlen($img); //得到图片大小
            $allowSize = 1024 * $config['maxSize'];
            if ($uriSize > $allowSize) {
                array_push($list, "maxSize error");//大小验证
                continue;
            }
            ob_flush();
            $savePath = $config['savePath'];
            //创建保存位置
            if (!file_exists($savePath)) {
                mkdir($savePath, 0777, true);
            }
            //写入文件
            $tmpName = rand(1, 10000) . time();
            try {
                $img  = file_get_contents($imgUrl);
                $flag = file_put_contents($savePath . $tmpName, $img);//一定注意权限啊
                if (!$flag)
                    throw new Exception('图片读取失败');
                $key = $this->qiniuUpload($savePath . $tmpName);
                if (!$key)
                    throw new Exception($key->message());
                array_push($list, [
                    'source' => $imgUrl,
                    'state'  => 'SUCCESS',
                    'url'    => $this->config['imageUrlPrefix'] . $key['key']
                ]);
                unlink($savePath . $tmpName);
            } catch (\Exception $e) {
                array_push($list, ['state' => 'ERROR', 'source' => $imgUrl, 'msg' => $e->getMessage()]);
            }
        }

        return json_encode(['state' => count($list) ? 'SUCCESS' : 'ERROR', 'list' => $list]);
    }

    private function qiniuUpload($file)
    {
        $auth      = new Auth(config('qiniu_access'), config('qiniu_secret'));
        $token     = $auth->uploadToken(config('qiniu_bucket'));
        $key       = date('YmdHis') . mt_rand(100, 999);
        $uploadMgr = new UploadManager();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $file);
        if ($err !== null) {
            return $err;
        } else {
            return $ret;
        }
    }

    public function uploadToken()
    {
        $auth   = new Auth(config('qiniu_access'), config('qiniu_secret'));
        $policy = [
            'returnBody' => '{"url":$(key),"key":$(key),"state":"SUCCESS"}'
        ];
        $token  = $auth->uploadToken(config('qiniu_bucket'), null, 3600, $policy);
        return json(['token' => $token, 'cdn' => config('qiniu_cdn')]);
    }
}