<?php
/**
 * 后台页面导航业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BookModel;

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
    public function list($cond, int $page, int $pageSize){
        $books = BookModel::where($cond)->hidden(BookModel::hiddenFields())->page($page, $pageSize)->select();
        return $books;
    }
    /**
     * 书籍列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function listCount($cond){
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
}

