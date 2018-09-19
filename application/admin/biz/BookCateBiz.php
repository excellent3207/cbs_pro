<?php
/**
 * 图书分类业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BookCateModel;

class BookCateBiz{
    public function getRole(){
        
    }
    /**
     * 获取书籍详情
     * @param unknown $id
     * @return \app\common\model\BookCateModel
     */
    public function get($id){
        $res = BookCateModel::get($id)->hideField()->toArray();
        return $res;
    }
    /**
     * 所有分类
     */
    public function all(){
        $books = BookCateModel::where([])->hidden(BookCateModel::hiddenFields())->order('orderid')->select();
        return $books;
    }
    /**
     * 图书分类列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function list($cond, int $page, int $pageSize){
        $books = BookCateModel::where($cond)->hidden(BookCateModel::hiddenFields())->page($page, $pageSize)->select();
        return $books;
    }
    /**
     * 图书分类列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function listCount($cond){
        return BookCateModel::where($cond)->count('id');
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new BookCateModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑图书分类失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建图书分类失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param unknown $navid
     * @return unknown
     */
    public function del($ids){
        return BookCateModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 分类排序
     * @param unknown $id
     * @return unknown
     */
    public function editOrder($id, $orderid){
        $model = new BookCateModel();
        return $model->save(['orderid' => $orderid], ['id' => $id]);
    }
}

