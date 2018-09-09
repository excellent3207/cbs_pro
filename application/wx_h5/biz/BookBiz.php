<?php
/**
 * 图书业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;

class BookBiz{
    /**
     * 推荐列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function recommend($pageSize){
        $hiddenFields = ['book_no','author','img_info','price','plotter','ppt_img','ppt_source','demo_chapter','standard',
            'paper_img','paper_source','publishtime','show_time','recommend_time','description'];
        $hiddenFields = array_merge($hiddenFields, BookModel::hiddenFields());
        return BookModel::where([['show_time', '<>', 0],['recommend_time', '<>', 0]])->order('recommend_time desc')->hidden($hiddenFields)->select();
    }
    /**
     * 图书列表
     * @param unknown $cond
     * @param unknown $order
     * @param unknown $page
     * @param unknown $pageSize
     * @return unknown
     */
    public function list($cond, $order, $page, $pageSize){
        $hiddenFields = ['img_info','plotter','ppt_img','ppt_source','demo_chapter','standard',
            'paper_img','paper_source','show_time','recommend_time','description'];
        $hiddenFields = array_merge($hiddenFields, BookModel::hiddenFields());
        $books = BookModel::where([['show_time', '<>', 0]])->where($cond)->order('recommend_time desc')->hidden($hiddenFields)->page($page, $pageSize)->select();
        foreach($books as &$book){
            $book->img_list = formatUrl($book->img_list);
        }
        return $books;
    }
}



