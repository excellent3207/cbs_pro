<?php
/**
 * 图书
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\admin\biz\BookBiz;
use app\common\validate\BookValidate;

class Book{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 书籍列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['bookList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new BookBiz();
        $cond = [];
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $page, $pageSize);
        return view('', ['list' => $list]);
    }
    /**
     * 创建用户
     * @return \think\response\Redirect|unknown
     */
    public function add(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BookValidate();
            $error = '';
            if(!$validate->check($data, [], 'create')){
                $error = $validate->getError();
            }else{
                $biz = new BookBiz();
                try{
                    $biz->add($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('book/add')->with('book_add_data', $data);
            }else{
                return redirect(getPageHistory('bookList'));
            }
        }
        $book = session('book_add_data');
        if(empty($book)) $book = [];
        return view('', ['data' => $book, 'prePage' => getPageHistory('bookList')]);
    }
    /**
     * 编辑用户
     * @return \think\response\Redirect|unknown
     */
    public function edit(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BookValidate();
            $error = '';
            if(!$validate->check($data, [], 'edit')){
                $error = $validate->getError();
            }else{
                $biz = new BookBiz();
                try{
                    $biz->edit($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('book/edit')->with('book_edit_data', $data);
            }else{
                return redirect(getPageHistory('bookList'));
            }
        }
        $biz = new BookBiz();
        $id = $this->request->get('id');
        $book = session('book_edit_data');
        if(empty($book)){
            $book = $biz->get($id);
        }
        return view('', ['data' => $book, 'prePage' => getPageHistory('bookList')]);
    }
    /**
     * 删除导航
     * @return \think\response\Json
     */
    public function del(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
