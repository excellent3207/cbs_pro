<?php
/**
 * 用户业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;

class UserBiz{
    /**
     * 我的书架
     */
    public function myBooks($cond, $order, $page, $pageSize){
        $user = config('user');
        $books = $user->books()->where($cond)->page($page, $pageSize)->order($order)->hidden(BookModel::hiddenFields())->showQuery()->select();
        return $books;
    }
}
