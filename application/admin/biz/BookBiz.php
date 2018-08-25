<?php
/**
 * 后台页面导航业务层
 */
namespace app\admin\biz;

use app\admin\model\NavAdminModel;
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
        return BookModel::get($id)->hideField();
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
     * 创建
     * @param unknown $data
     * @throws AppException
     * @return unknown
     */
    public function add($data){
        $model = new BookModel();
        $res = $model->allowField(TRUE)->save($data);
        if(!$res) throw new AppException('创建书籍失败');
        return $model->id;
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function edit($data){
        $model = new BookModel();
        return $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
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

