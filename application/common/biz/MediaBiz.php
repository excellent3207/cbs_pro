<?php 
/**
 * OSS资源文件管理
 * author：xjp
 * create：2017.4.8
 */
namespace app\common\biz;

use app\common\model\AliOOS;
use app\common\CbsTool;

class MediaBiz{
    /**
     * 测试获取sts凭证 注意：$dir必须选择
     * @throws SyhException
     * @return string[]|NULL[]
     */
    public function testSts(){
        $ali = new AliOOS();
        $dir = 'test/'.date('Ymd').'/'.date('His').CbsTool::randString(6);
        $res = $ali->sts([AliOOS::POLICY_ACTION_PUT_OBJECT], [AliOOS::BUCKET_RESOURCE."/{$dir}*"]);
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
        $dir = 'img/'.date('Ymd').'/'.date('His').CbsTool::randString(6);
        $res = $ali->sts([AliOOS::POLICY_ACTION_PUT_OBJECT], [AliOOS::BUCKET_RESOURCE."/{$dir}*"]);
        $res['dir'] = $dir;
        $res['domain'] = $ali->getBucketDomain(AliOOS::BUCKET_RESOURCE);
        $res['bucket'] = AliOOS::BUCKET_RESOURCE;
        $res['region'] = 'oss-cn-beijing';
        return $res;
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
}
?>