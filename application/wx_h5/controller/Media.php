<?php
/**
 * 多媒体
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;


use think\Request;
use app\common\biz\MediaBiz;

class Media{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 获取视频播放凭证
     * @return \think\response\View
     */
    public function videoPlayAuth(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $vid = $this->request->post('vid');
            $biz = new MediaBiz();
            $ret['data'] = $biz->videoPlayAuth($vid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}

