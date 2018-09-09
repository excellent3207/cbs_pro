<?php
/**
 * 地址
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;

use think\Request;
use app\wx_h5\biz\LocationBiz;

class Location{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 省
     * @return \think\response\View
     */
    public function provinces(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new LocationBiz();
            $ret['data'] = $biz->provinces();
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 市
     * @return \think\response\View
     */
    public function citys(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new LocationBiz();
            $provinceid = $this->request->post('provinceid');
            $ret['data'] = $biz->citys($provinceid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 县
     * @return \think\response\View
     */
    public function countys(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new LocationBiz();
            $cityid = $this->request->post('cityid');
            $ret['data'] = $biz->countys($cityid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}

