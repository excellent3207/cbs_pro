<?php 
/**
 * OSS资源文件管理
 * author：xjp
 * create：2017.4.8
 */
namespace app\common\biz;

use app\common\model\AliOOS;
use app\common\AppTool;
use app\common\model\Services_JSON;

class MediaBiz{
    /**
     * 测试获取sts凭证 注意：$dir必须选择
     * @throws SyhException
     * @return string[]|NULL[]
     */
    public function testSts(){
        $ali = new AliOOS();
        $dir = 'test/'.date('Ymd').'/'.date('His').AppTool::randString(6);
        $res = $ali->uploadSts([AliOOS::POLICY_ACTION_PUT_OBJECT], [AliOOS::BUCKET_RESOURCE."/{$dir}*"]);
        $res['dir'] = $dir;
        $res['domain'] = $ali->getBucketDomain(AliOOS::BUCKET_RESOURCE);
        $res['bucket'] = AliOOS::BUCKET_RESOURCE;
        $res['region'] = 'oss-cn-beijing';
        return $res;
    }
    /**
     * 图片获取sts凭证 注意：$dir必须选择
     * @throws SyhException
     * @return string[]|NULL[]
     */
    public function imgSts(){
        $ali = new AliOOS();
        $dir = 'img/'.date('Ymd').'/'.date('His').AppTool::randString(6);
        $res = $ali->uploadSts([AliOOS::POLICY_ACTION_PUT_OBJECT], [AliOOS::BUCKET_RESOURCE."/{$dir}*"]);
        $res['dir'] = $dir;
        $res['domain'] = $ali->getBucketDomain(AliOOS::BUCKET_RESOURCE);
        $res['bucket'] = AliOOS::BUCKET_RESOURCE;
        $res['region'] = 'oss-cn-beijing';
        return $res;
    }
    /**
     * 文件获取sts凭证 注意：$dir必须选择
     * @throws SyhException
     * @return string[]|NULL[]
     */
    public function fileSts(){
        $ali = new AliOOS();
        $dir = 'file/'.date('Ymd').'/'.date('His').AppTool::randString(6);
        $res = $ali->uploadSts([AliOOS::POLICY_ACTION_PUT_OBJECT], [AliOOS::BUCKET_RESOURCE."/{$dir}*"]);
        $res['dir'] = $dir;
        $res['domain'] = $ali->getBucketDomain(AliOOS::BUCKET_RESOURCE);
        $res['bucket'] = AliOOS::BUCKET_RESOURCE;
        $res['region'] = 'oss-cn-beijing';
        return $res;
    }
    /**
     * 获取视频播放凭证
     * @param unknown $vid
     * @return unknown|mixed
     */
    public function videoPlayAuth($vid){
        $ali = new AliOOS();
        return $ali->getPlayAuth($vid);
    }
    /**
     * 获取视频上传凭证
     * @return \app\media\model\unknown|mixed
     */
    public function vodCreateVideo(){
        $ali = new AliOOS();
        $res = $ali->create_upload_video();
        return $res;
    }
    /**
     * 刷新视频上传凭证
     * @param unknown $vid
     * @return \app\media\model\unknown|mixed
     */
    public function vodRefreshVideo($vid){
        $ali = new AliOOS();
        $res = $ali->refresh_upload_video($vid);
        return $res;
    }
    /**
     * 阿里视频认证
     *
     */
    public function vodauthjson(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $mediaBiz = new MediaBiz();
            $vodAuth = $mediaBiz->vodCreateVideo();
            $ret['data'] = $vodAuth;
        }catch(SyhException $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return Response::create($ret, 'json');
    }
    /**
     * 刷新阿里视频认证
     *
     */
    public function refreshvodauthjson(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $vid = input('get.vid', '');
            $mediaBiz = new MediaBiz();
            $vodAuth = $mediaBiz->vodRefreshVideo($vid);
            $ret['data'] = $vodAuth;
        }catch(SyhException $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return Response::create($ret, 'json');
    }
    /**
     * 富文本编辑器文件上传
     */
    public function editorUpload(){
        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch($_FILES['imgFile']['error']){
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '文件只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择文件。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            $this->alert($error);
        }
        if(!empty($_FILES)){
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            
            $filename = 'img/kindeditor/'.date('Ymd').'/'.date('His').AppTool::randString(6).'.'.$file_ext;
            try{
                $oosModel = new AliOOS();
                $mediaUrl = $oosModel->multiUploadFile(AliOOS::BUCKET_RESOURCE, $filename, $tmp_name);
                $mediaUrl = AppTool::formatImgUrl($mediaUrl);
                header('Content-type: text/html; charset=UTF-8');
                $json = new Services_JSON();
                echo $json->encode(array('error' => 0, 'url' => $mediaUrl));
                exit;
            }catch(SyhException $e){
                $this->alert($e->getMessage());
            }
        }
    }
}
?>