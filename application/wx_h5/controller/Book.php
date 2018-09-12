<?php
/**
 * 图书
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;


use think\Request;
use app\wx_h5\biz\BookBiz;

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
        }catch(\Exception $e){
            return view('common/error', ['msg' => $e->getMessage()]);
        }
        return view('', ['type' => $type]);
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
                array_push($cond, ['name', 'like', $name.'%']);
            }
            $biz->recordSearch($name);
            $type = $this->request->get('type');
            if($type){
                array_push($cond, ['type', '=', $type]);
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

