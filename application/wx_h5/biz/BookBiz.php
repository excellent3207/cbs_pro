<?php
/**
 * 图书业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;
use app\common\AppRedis;
use app\common\model\BookVideoModel;

class BookBiz{
    const SEARCH_RECORD_KEY = 'search_record_';
    /**
     * 推荐列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function recommend($pageSize){
        $hiddenFields = ['book_no','author','img_info','price','plotter','ppt','ppt_source','demo_chapter','standard',
            'paper','paper_source','publishtime','show_time','recommend_time','description'];
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
        $hiddenFields = ['img_info','plotter','ppt','ppt_source','demo_chapter','standard',
            'paper','paper_source','show_time','recommend_time','description'];
        $hiddenFields = array_merge($hiddenFields, BookModel::hiddenFields());
        $books = BookModel::where([['show_time', '<>', 0]])->order($order)->where($cond)->order('recommend_time desc')->hidden($hiddenFields)->page($page, $pageSize)->select();
        foreach($books as &$book){
            $book->img_list = formatUrl($book->img_list);
        }
        $count = BookModel::where([['show_time', '<>', 0]])->where($cond)->count('id');
        return ['list' => $books, 'count' => $count];
    }
    /**
     * 根据id获取图书信息
     * @param unknown $id
     * @return \app\common\model\BookModel
     */
    public function get($id){
        $res = BookModel::get($id);
        if(!empty($res)){
            $res = $res->hideField();
            $res->videos = $res->videos()->where([['show_time', '<>', 0]])->select()->hidden(BookVideoModel::hiddenFields());
            $res->img_list = formatUrl($res->img_list);
            $res->img_info = formatUrl($res->img_info);
            $res->ppt_source = formatUrl($res->ppt_source);
            $res->paper_source = formatUrl($res->paper_source);
            $res->standard_source = formatUrl($res->standard_source);
            $res->answer_source = formatUrl($res->answer_source);
            $res->expand_source = formatUrl($res->expand_source);
            $userBiz = new UserBiz();
            $res->in_shelf = $userBiz->checkInShelf($res->id);
            $res->get_chapter_demo = $userBiz->checkHasDemoChapter($res->id);
            $res->hasVideo = !$res->videos->isEmpty();
            $res->demo_chapter = formatUrl($res->demo_chapter);
        }
        return $res;
    }
    /**
     * 图书下视频列表
     * @param unknown $bookid
     */
    public function videos($bookid){
        $bookModel = new BookModel();
        $bookModel->id = $bookid;
        $videos = $bookModel->videos()->where([['show_time', '<>', 0]])->select()->hidden(BookVideoModel::hiddenFields());;
        if($videos->isEmpty()) throw new \Exception('该图书下没有视频');
        return $videos;
    }
    /**
     * 搜索记录
     * @param unknown $searchKey
     */
    public function recordSearch($searchKey){
        if(!$searchKey) return;
        $user = config('user');
        $redis = AppRedis::instance();
        $keys = $redis->hget(self::SEARCH_RECORD_KEY, $user->id);
        $refresh = false;
        if(!empty($keys)){
            if(!in_array($searchKey, $keys)){
                if(count($keys) >= 6){
                    array_pop($keys);
                }
                array_unshift($keys, $searchKey);
                $refresh = true;
            }
        }else{
            $keys = [$searchKey];
            $refresh = true;
        }
        if($refresh){
            $redis->hset(self::SEARCH_RECORD_KEY, $user->id, json_encode($keys));
        }
    }
    /**
     * 搜索记录列表
     * @return array|\app\common\不存在返回空列表
     */
    public function getSearchRecord(){
        $user = config('user');
        $redis = AppRedis::instance();
        $keys = $redis->hget(self::SEARCH_RECORD_KEY, $user->id);
        if(empty($keys)) $keys = [];
        return $keys;
    }
}



