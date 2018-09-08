<?php
/**
 * 用户
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;

use think\Request;
use app\wx_h5\biz\UserBiz;

class User{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 我的书架
     * @return \think\response\View
     */
    public function myBooks(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new UserBiz();
            $cond = [];
            $order = [];
            $page = $this->request->get('page', 1);
            $pageSize = 10;
            $name = $this->request->get('name');
            if($name){
                array_push($cond, ['name', 'like', $name.'%']);
            }
            $ret['data'] = $biz->myBooks($cond, $order, $page, $pageSize);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}

