<?php
/**
 * 图书
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;


use think\Request;
use app\wx_h5\biz\BookBiz;
use app\wx_h5\biz\BookCateBiz;
use app\common\biz\MediaBiz;
use app\common\model\AliOOS;

class Book{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 图书列表/分类
     * @return \think\response\View
     */
    public function index(){
        try{
            $type = $this->request->get('type', 0);
            $cateBiz = new BookCateBiz();
            $cates = $cateBiz->listByType($type);
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['type' => $type, 'cates' => $cates]);
    }
    /**
     * 图书列表/分类
     * @return \think\response\View
     */
    public function recommend(){
        return view('', []);
    }
    /**
     * 图书详情
     * @return \think\response\View
     */
    public function get(){
        try{
            $id = $this->request->get('id');
            $biz = new BookBiz();
            $book = $biz->get($id);
            $mediaBiz = new MediaBiz();
            if(!$book['videos']->isEmpty()){
                $curVid = $book['videos'][0]['ali_vid'];
            }else{
                $curVid = '';
            }
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['book' => $book, 'curVid' => $curVid]);
    }
    /**
     * 图书列表
     * @return \think\response\View
     */
    public function list(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new BookBiz();
            $cond = [];
            $order = [];
            $page = $this->request->get('page', 1);
            $pageSize = 10;
            $name = $this->request->get('name');
            if($name){
                array_push($cond, ['name', 'like', '%'.$name.'%']);
            }
            $biz->recordSearch($name);
            $type = $this->request->get('type');
            if($type){
                array_push($cond, ['type', '=', $type]);
            }
            $cateid = $this->request->get('cateid');
            if($cateid){
                array_push($cond, ['cateid', '=', $cateid]);
            }
            $is_recommend = $this->request->get('is_recommend', -1);
            if($is_recommend != -1){
                array_push($cond, ['recommend_time', '<>', 0]);
                $order['recommend_time'] = 'desc';
            }
            $ret['data'] = $biz->list($cond, $order, $page, $pageSize);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 图书搜索
     * @return \think\response\View
     */
    public function search(){
        try{
            $type = $this->request->get('type', 0);
            $biz = new BookBiz();
            $searchKeys = $biz->getSearchRecord();
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['type' => $type, 'searchKeys' => $searchKeys]);
    }
}

