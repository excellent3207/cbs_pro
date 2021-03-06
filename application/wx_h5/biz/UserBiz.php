<?php
/**
 * 用户业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;
use app\common\exception\AppException;
use app\common\model\UserModel;
use app\common\model\DraftModel;
use app\common\model\UserDemoChapterModel;

class UserBiz{
    /**
     * 我的书架
     */
    public function myBooks($cond, $page, $pageSize){
        $user = config('user');
        $hiddenFields = BookModel::hiddenFields();
        array_push($hiddenFields, 'ppt_source', 'paper_source');
        $books = $user->books()->where($cond)->where([['show_time', '<>', 0]])
            ->page($page, $pageSize)->order(['pivot.add_time'=> 'desc'])->hidden($hiddenFields)->select();
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
        if($this->checkInShelf($bookid)) throw new \Exception('图书已经在你的书架了');
        return $user->books()->attach($bookid, ['add_time' => $_SERVER['REQUEST_TIME']]);
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
     * 判断是否有样章
     * @param unknown $bookid
     * @return boolean
     */
    public function checkHasDemoChapter($bookid){
        $demoChapter = new UserDemoChapterModel();
        $user = config('user');
        $cond = ['userid' => $user['id'], 'bookid' => $bookid];
        $count = UserDemoChapterModel::where($cond)->count('id');
        return $count > 0;
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
     * @param unknown $page
     * @param unknown $pageSize
     */
    public function myDrafts($cond, $page, $pageSize){
        $user = config('user');
        $hiddenFields = DraftModel::hiddenFields();
        array_push($hiddenFields, 'content');
        $drafts = $user->drafts()->where([['show_time', '<>', 0]])->order(['pivot.add_time'=> 'desc'])
            ->page($page, $pageSize)->hidden($hiddenFields)->select();
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
        $count = $user->drafts()->where([['show_time', '<>', 0],['cbs_draft.id', '=', $draftid]])->count('cbs_draft.id');
        return $count>0;
    }
    /**
     * 收藏文稿
     * @param unknown $draftid
     * @return unknown
     */
    public function doCollectDraft($draftid){
        $user = config('user');
        return $user->drafts()->attach($draftid, ['add_time' => $_SERVER['REQUEST_TIME']]);
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
