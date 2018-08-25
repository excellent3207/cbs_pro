<?php
/**
 * 多媒体
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use app\common\biz\MediaBiz;
use think\Request;

class Media{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    public function test(){
        return view('', []);
    }
    /**
     * 视频认证
     * @return \think\response\Json
     */
    public function vodAuth(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new MediaBiz();
            $ret['data'] = $biz->vodCreateVideo();
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 刷新视频认证
     * @return \think\response\Json
     */
    public function refreshVodAuth(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $vid = $this->request->get('vid');
            $biz = new MediaBiz();
            $ret['data'] = $biz->vodRefreshVideo($vid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 图片sts授权
     * @return \think\response\Json
     */
    public function imgSts(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new MediaBiz();
            $ret['data'] = $biz->imgSts();
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}