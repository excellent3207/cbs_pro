<?php
/**
 * 图书业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BookModel;
use app\common\model\BookCateModel;
use think\Db;
use think\Exception;

class BookBiz{
    public function getRole(){
        
    }
    /**
     * 获取书籍详情
     * @param unknown $id
     * @return \app\common\model\BookModel
     */
    public function get($id){
        $res = BookModel::get($id)->hideField()->toArray();
        if(!empty($res)){
            $res['publishtime'] = date('Y-m-d H:i', $res['publishtime']);
        }
        return $res;
    }
    /**
     * 书籍列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function list($cond, $cateid, int $page, int $pageSize){
        if($cateid){
            $cate = BookCateModel::get($cateid);
            $books = $cate->books()->select();
        }else{
            $books = BookModel::where($cond)->hidden(BookModel::hiddenFields())->page($page, $pageSize)->select();
        }
        $books->load('cates');
        return $books;
    }
    /**
     * 书籍列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function listCount($cond, $cateid){
        if($cateid){
            
        }
        return BookModel::where($cond)->count('id');
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new BookModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑书籍失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建书籍失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param unknown $navid
     * @return unknown
     */
    public function del($ids){
        return BookModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 前端展示图书
     * @param unknown $id
     * @return unknown
     */
    public function doShow($id){
        $model = new BookModel();
        return $model->save(['show_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消前端展示图书
     * @param unknown $id
     * @return unknown
     */
    public function cancelShow($id){
        $model = new BookModel();
        return $model->save(['show_time' => 0], ['id' => $id]);
    }
    /**
     * 推荐图书
     * @param unknown $id
     * @return unknown
     */
    public function doRecomm($id){
        $model = new BookModel();
        return $model->save(['recommend_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消推荐图书
     * @param unknown $id
     * @return unknown
     */
    public function cancelRecomm($id){
        $model = new BookModel();
        return $model->save(['recommend_time' => 0], ['id' => $id]);
    }
    /**
     * 图书添加分类
     * @param unknown $bookid
     * @param unknown $cateids
     */
    public function addCate($bookid, $cateids){
        if(!is_array($cateids)){
            $cateids = explode(',', $cateids);
        }
        $cateids = array_filter($cateids);
        $book = new BookModel();
        $book->id = $bookid;
        $oldCateids = $book->cates()->column('cbs_book_cate.id');
        $addCateids = array_diff($cateids, $oldCateids);
        $delCateids = array_diff($oldCateids, $cateids);
        Db::startTrans();
        try{
            if(!empty($addCateids)){
                $book->cates()->attach($addCateids);
            }
            if(!empty($delCateids)){
                $book->cates()->detach($delCateids);
            }
            Db::commit();
        }catch(Exception $e){
            Db::rollback();
            throw $e;
        }
        return true;
    }
}

