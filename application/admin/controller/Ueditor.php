<?php
/**
 * Created by PhpStorm.
 * User: 85210755@qq.com
 * NickName: 柏宇娜
 * Date: 18-11-11 上午10:02
 */

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\facade\App;

class Ueditor extends Controller
{
    public function initialize()
    {
        if (input('action', '') == 'config'){
            json(json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(env('config_path') . "config.json")), true))->send();
            exit;
        }
    }

    public function index()
    {
        $action=input('action');
        switch ($action){
            /* 上传图片 */
            case 'uploadimage':
                dump(input('param.'));die;
                /* 上传涂鸦 */
            case 'uploadscrawl':
                /* 上传视频 */
            case 'uploadvideo':
                /* 上传文件 */
            case 'uploadfile':
                $result = include("action_upload.php");
                break;

            /* 列出图片 */
            case 'listimage':
                $result = include("action_list.php");
                break;
            /* 列出文件 */
            case 'listfile':
                $result = include("action_list.php");
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = include("action_crawler.php");
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }

    }

    public function imgupload()
    {
        // 上传图片框中的描述表单名称，
        $pictitle = input('pictitle');
        $dir = input('dir');
        $title = htmlspecialchars($pictitle , ENT_QUOTES);
        $path = htmlspecialchars($dir, ENT_QUOTES);

        //$input_file           ['upfile'] = $info['Filedata'];  一个是上传插件里面来的, 另外一个是 文章编辑器里面来的
        // 获取表单上传文件
        $file = request()->file('file');

        if(empty($file))
            $file = request()->file('upfile');
        $result = $this->validate(
            ['file2' => $file],
            ['file2'=>'image|fileSize:20000000'],
            ['file2.image' => '上传文件必须为图片','file2.fileSize' => '上传文件过大']
        );
        dump($result);die;
        if(true !== $result){
            $state = "ERROR" . $result;
        }else{
            // 移动到框架应用根目录/public/uploads/ 目录下
            //$this->savePath = $this->savePath.date('Y').'/'.date('m-d').'/';
            //echo $this->savePath;
            $info = $file->rule('date')->move('/upload/');
            if ($info)
                $state = "SUCCESS";
            else
                $state = "ERROR" . $file->getError();
            $return_data['url'] = '/upload/'.$info->getSaveName();
        }
        $name=$info->getSaveName();
        $result=$this->oss_upload('/upload/'.$name);
        unset($info);
        unlink(Env::get('root_path').'/upload/'.$name);
        $return_data['url'] = $result;
        $return_data['title'] = $title;
        $return_data['original'] = $name; // 这里好像没啥用 暂时注释起来
        $return_data['state'] = $state;
        $return_data['path'] = $path;
        $this->ajaxReturn($return_data,'json');
    }
}