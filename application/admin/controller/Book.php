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
use app\common\Pagination;
use app\admin\biz\BookCateBiz;
use app\common\validate\BookVideoValidate;
use app\admin\biz\Qrcode;

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
        $cateid = 0;
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['name']) && $params['name']){
            array_push($cond, ['name', 'like', '%'.$params['name'].'%']);
        }
        if(isset($params['book_no']) && $params['book_no']){
            array_push($cond, ['book_no', 'like', '%'.$params['book_no'].'%']);
        }
        if(isset($params['author']) && $params['author']){
            array_push($cond, ['author', 'like', '%'.$params['author'].'%']);
        }
        if(isset($params['plotter']) && $params['plotter']){
            array_push($cond, ['plotter', 'like', '%'.$params['plotter'].'%']);
        }
        if(isset($params['is_show']) && $params['is_show'] != -1){
            $c = $params['is_show'] ? '<>' : '=';
            array_push($cond, ['show_time', $c, 0]);
        }
        if(isset($params['is_recommend']) && $params['is_recommend'] != -1){
            $c = $params['is_recommend'] ? '<>' : '=';
            array_push($cond, ['recommend_time', $c, 0]);
        }
        if(isset($params['cateid']) && $params['cateid']){
            array_push($cond, ['cateid', '=', $params['cateid']]);
        }
        if(isset($params['type']) && $params['type']){
            array_push($cond, ['type', '=', $params['type']]);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $cateid, $page, $pageSize);
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond, $cateid));
        $cateBiz = new BookCateBiz();
        $cates = $cateBiz->all();
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination, 'cates' => $cates]);
    }
    /**
     * 图书二维码
     */
    public function qrcode(){
        $bookid = $this->request->get('bookid');
        $biz = new Qrcode();
        $biz->download('http://h5.igniter.vip/wx_h5/book/get?id='.$bookid);
    }
    /**
     * 编辑
     * @return \think\response\Redirect
     */
    public function save(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BookValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new BookBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('book/save')->with('book_save_data', $data);
            }else{
                return redirect(getPageHistory('bookList'));
            }
        }
        $biz = new BookBiz();
        $id = $this->request->get('id');
        $book = session('book_save_data');
        if(empty($book) && $id){
            $book = $biz->get($id);
        }
        return view('', ['data' => $book, 'prePage' => getPageHistory('bookList')]);
    }
    /**
     * 删除
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
    /**
     * 前端展示图书
     * @return \think\response\Json
     */
    public function doShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->doShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消前端展示图书
     * @return \think\response\Json
     */
    public function cancelShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->cancelShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 推荐图书
     * @return \think\response\Json
     */
    public function doRecomm(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->doRecomm($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消推荐图书
     * @return \think\response\Json
     */
    public function cancelRecomm(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->cancelRecomm($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 图书添加分类
     * @return \think\response\Json
     */
    public function addCate(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $bookid = $this->request->post('bookid');
        $cateid = $this->request->post('cateid');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->addCate($bookid, $cateid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 书籍视频列表
     * @return \think\response\View
     */
    public function videos(){
        setPageHistory(['bookVideoList' => \think\facade\Request::url()], false);
        $params = $this->request->get();
        $biz = new BookBiz();
        $cond = [];
        $bookid = $this->request->get('bookid');
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['is_show']) && $params['is_show'] != -1){
            $c = $params['is_show'] ? '<>' : '=';
            array_push($cond, ['show_time', $c, 0]);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        try{
            $list = $biz->videos($bookid, $cond, $page, $pageSize);
        }catch(\Exception $e){
            echo $e->getMessage();exit;
        }
        $pagination = new Pagination($page, $pageSize, $biz->videoCount($bookid, $cond));
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination, 
            'prePage' => getPageHistory('bookList'), 'bookid' => $bookid]);
    }
    /**
     * 编辑视频
     * @return \think\response\Redirect
     */
    public function saveVideo(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BookVideoValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new BookBiz();
                try{
                    $biz->saveVideo($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('book/savevideo')->with('book_video_save_data', $data);
            }else{
                return redirect(getPageHistory('bookVideoList'));
            }
        }
        $bookid = $this->request->get('bookid');
        $biz = new BookBiz();
        $id = $this->request->get('id');
        $video = session('book_video_save_data');
        if(empty($video) && $id){
            $video = $biz->getVideo($id);
        }else{
            $video['bookid'] = $bookid;
        }
        return view('', ['data' => $video, 'prePage' => getPageHistory('bookVideoList'), 'bookList' => getPageHistory('bookList')]);
    }
    /**
     * 删除视频
     * @return \think\response\Json
     */
    public function delVideo(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->delVideo($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 前端展示视频
     * @return \think\response\Json
     */
    public function doVideoShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->doVideoShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消前端展示视频
     * @return \think\response\Json
     */
    public function cancelVideoShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BookBiz();
        try{
            $ret['data'] = $biz->cancelVideoShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 图书批量导入
     * @return \think\response\Redirect
     */
    public function import(){
        $file = $this->request->file('file');
        if(!empty($file)){
            $biz = new BookBiz();
            $tmpName = $file->getFileInfo()->getPathname();
            $res = $biz->import($tmpName);
            return redirect('book/import')->with('book_import_data', $res);
        }else{
            $data = session('book_import_data');
            return view('', ['prePage' => getPageHistory('bookList'), 'data' => $data]);
        }
    }
}
