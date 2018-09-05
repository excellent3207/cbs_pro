<?php
/**
 * 图书分类
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\admin\biz\BookCateBiz;
use app\common\Pagination;
use app\common\validate\BookCateValidate;

class BookCate{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 图书分类列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['bookCateList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new BookCateBiz();
        $cond = [];
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['catename']) && $params['catename']){
            array_push($cond, ['catename', 'like', '%'.$params['catename'].'%']);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $page, $pageSize);
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond));
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination]);
    }
    /**
     * 图书分类选择列表列表
     * @return \think\response\View
     */
    public function listSelect(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $cond = [];
        $name = input('get.name');
        if($name){
            $cond['catename'] = ['like', '%'.$name.'%'];
        }
        $biz = new BookCateBiz();
        $cond = [];
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $ret['data'] = $biz->list($cond, $page, $pageSize);
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond));
        $ret['page'] = $pagination->render2();
        return json($ret);
    }
    /**
     * 编辑图书分类
     * @return \think\response\Redirect|unknown
     */
    public function save(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BookCateValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new BookCateBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('bookcate/save')->with('bookcate_save_data', $data);
            }else{
                return redirect(getPageHistory('bookCateList'));
            }
        }
        $biz = new BookCateBiz();
        $id = $this->request->get('id');
        $book = session('bookcate_save_data');
        if(empty($book) && $id){
            $book = $biz->get($id);
        }
        return view('', ['data' => $book, 'prePage' => getPageHistory('bookCateList')]);
    }
    /**
     * 删除图书分类
     * @return \think\response\Json
     */
    public function del(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new BookCateBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 编辑分类排序
     * @return \think\response\Json
     */
    public function editOrder(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $orderid = $this->request->post('orderid');
        $biz = new BookCateBiz();
        try{
            $ret['data'] = $biz->editOrder($id, $orderid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
