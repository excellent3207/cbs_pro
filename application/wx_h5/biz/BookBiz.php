<?php
/**
 * 图书业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookModel;
use app\common\AppRedis;

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
    /**
     * 搜索记录
     * @param unknown $searchKey
     */
    public function recordSearch($searchKey){
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



