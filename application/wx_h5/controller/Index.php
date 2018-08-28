<?php
/**
 * 
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;

use app\wx_h5\biz\BannerBiz;

class Index{
    /**
     * 首页
     * @return \think\response\View
     */
    public function index(){
        try{
           $biz = new BannerBiz();
           $banners = $biz->all();
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['banners' => $banners]);
    }
}
