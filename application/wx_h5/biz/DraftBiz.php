<?php
/**
 * 后台文稿业务层
 */
namespace app\wx_h5\biz;

use app\common\model\DraftModel;
use app\common\AppTool;

class DraftBiz{
    public function getRole(){
        
    }
    /**
     * 获取文稿详情
     * @param unknown $id
     * @return \app\common\model\DraftModel
     */
    public function get($id){
        $res = DraftModel::get($id);
        if(!empty($res)){
            $res->hideField();
            $res->show_time = date('Y-m-d', $res->show_time);
            $userBiz = new UserBiz();
            $res->is_collect = $userBiz->checkDraftCollect($res->id);
        }
        return $res;
    }
    /**
     * 文稿列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function list($cond, int $page, int $pageSize){
        $drafts = DraftModel::where([['show_time', '<>', 0]])->order('create_time desc')->hidden(DraftModel::hiddenFields())->page($page, $pageSize)->select();
        $count = DraftModel::where([['show_time', '<>', 0]])->count('id');
        foreach($drafts as &$draft){
            $draft['img_list'] = AppTool::formatUrl($draft['img_list']);
        }
        return ['list' => $drafts, 'count' => $count];
    }
    /**
     * 文稿推荐列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function recommend(int $page, int $pageSize){
        $drafts = DraftModel::where([['show_time', '<>', 0], ['recommend_time', '<>', 0]])->hidden(DraftModel::hiddenFields())->page($page, $pageSize)->select();
        foreach($drafts as &$draft){
            $draft['img_list'] = formatUrl($draft['img_list']);
        }
        return $drafts;
    }
}

