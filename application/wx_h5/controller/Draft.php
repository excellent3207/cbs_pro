<?php
/**
 * 文稿
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;


use app\wx_h5\biz\DraftBiz;
use think\Request;

class Draft{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 文稿推荐
     * @return \think\response\View
     */
    public function recommend(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new DraftBiz();
            $page = $this->request->get('page', 1);
            $pageSize = 3;
            $ret['data'] = $biz->recommend($page, $pageSize);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}

