<?php
/**
 * 用户业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;
use app\common\exception\AppException;
use app\common\model\UserModel;

class UserBiz{
    /**
     * 我的书架
     */
    public function myBooks($cond, $order, $page, $pageSize){
        $user = config('user');
        $books = $user->books()->where($cond)->where([['show_time', '<>', 0]])
            ->page($page, $pageSize)->order($order)->hidden(BookModel::hiddenFields())->select();
        foreach($books as &$book){
            $book->img_list = formatUrl($book->img_list);
        }
        $count = $user->books()->where($cond)->where([['show_time', '<>', 0]])->count('cbs_book.id');
        return ['list' => $books, 'count' => $count];
    }
    /**
     * 创建或编辑用户
     * @param unknown $data
     * @throws AppException
     * @return boolean|unknown
     */
    public function save($data){
        $model = new UserModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑用户失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建用户失败');
            return $model->id;
        }
    }
}
