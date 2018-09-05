<?php
/**
 * 
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;

use app\wx_h5\biz\BannerBiz;
use app\wx_h5\biz\BookBiz;

class Index{
    /**
     * 首页
     * @return \think\response\View
     */
    public function index(){
        try{
           $bannerBiz = new BannerBiz();
           $banners = $bannerBiz->all();
           $bookBiz = new BookBiz();
           $books = $bookBiz->recommend(10);
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['banners' => $banners, 'books' => $books]);
    }
}
