<?php
/**
 * 用户
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\common\Pagination;
use app\admin\biz\UserBiz;

class User{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 用户列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['userList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new UserBiz();
        $cond = [];
        $cateid = 0;
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['phone']) && $params['phone']){
            array_push($cond, ['phone', 'like', '%'.$params['phone'].'%']);
        }
        if(isset($params['alias']) && $params['alias']){
            array_push($cond, ['alias', 'like', '%'.$params['alias'].'%']);
        }
        if(isset($params['school']) && $params['school']){
            array_push($cond, ['school', 'like', '%'.$params['school'].'%']);
        }
        if(isset($params['professional']) && $params['professional']){
            array_push($cond, ['professional', 'like', '%'.$params['professional'].'%']);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $res = $biz->list($cond,$page, $pageSize);
        $list = $res['list'];
        $pagination = new Pagination($page, $pageSize, $res['count']);
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination]);
    }
}
