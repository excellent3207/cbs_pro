<?php
/**
 * 图书业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;

class BookBiz{
    /**
     * banner列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function recommend($pageSize){
        $hiddenFields = ['book_no','author','img_info','price','has_ppt','plotter','ppt_img','ppt_source','demo_chapter','standard',
            'paper_img','paper_source','publishtime','show_time','recommend_time','description'];
        $hiddenFields = array_merge($hiddenFields, BookModel::hiddenFields());
        return BookModel::where([['recommend_time', '<>', 0]])->order('recommend_time desc')->hidden($hiddenFields)->select();
    }
}



