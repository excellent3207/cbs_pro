<?php
/**
 * 用户业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;
use app\common\exception\AppException;
use app\common\model\UserModel;
use app\common\model\DraftModel;

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
     * 加入书架
     */
    public function putInShelf($bookid){
        $user = config('user');
        return $user->books()->attach($bookid);
    }
    /**
     * 判断图书是否加入书架
     * @param unknown $bookid
     */
    public function checkInShelf($bookid){
        $user = config('user');
        $count = $user->books()->where('cbs_book.id',$bookid)->count('cbs_book.id');
        return $count>0;
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
    /**
     * 我收藏的文稿
     * @param unknown $cond
     * @param unknown $order
     * @param unknown $page
     * @param unknown $pageSize
     */
    public function myDrafts($cond, $order, $page, $pageSize){
        $user = config('user');
        $drafts = $user->drafts()->where([['show_time', '<>', 0]])
            ->page($page, $pageSize)->order($order)->hidden(DraftModel::hiddenFields())->select();
        foreach($drafts as &$draft){
            $draft->img_list = formatUrl($draft->img_list);
        }
        $count = $user->drafts()->where($cond)->where([['show_time', '<>', 0]])->count('cbs_draft.id');
        return ['list' => $drafts, 'count' => $count];
    }
    /**
     * 判断是否收藏文稿
     * @param unknown $draftid
     */
    public function checkDraftCollect($draftid){
        $user = config('user');
        $count = $user->drafts()->where([['show_time', '<>', 0]])->count('cbs_draft.id');
        return $count>0;
    }
    /**
     * 收藏文稿
     * @param unknown $draftid
     * @return unknown
     */
    public function doCollectDraft($draftid){
        $user = config('user');
        return $user->drafts()->attach($draftid);
    }
    /**
     * 取消收藏文稿
     * @param unknown $draftid
     * @return unknown
     */
    public function cancelCollectDraft($draftid){
        $user = config('user');
        return $user->drafts()->detach($draftid);
    }
}
