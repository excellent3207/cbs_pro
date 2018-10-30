<?php
/**
 * 后台文稿业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\DraftModel;

class DraftBiz{
    public function getRole(){
        
    }
    /**
     * 获取文稿详情
     * @param  $id
     * @return \app\common\model\DraftModel
     */
    public function get($id){
        $res = DraftModel::get($id)->hideField();
        return $res;
    }
    /**
     * 文稿列表
     * @param  $cond
     * @param int $page
     * @param int $pageSize
     * @return 
     */
    public function list($cond, int $page, int $pageSize){
        $drafts = DraftModel::where($cond)->order('create_time desc')->hidden(DraftModel::hiddenFields())->page($page, $pageSize)->select();
        return $drafts;
    }
    /**
     * 文稿列表数量
     * @param  $cond
     * @return 
     */
    public function listCount($cond){
        return DraftModel::where($cond)->count('id');
    }
    /**
     * 编辑
     * @param  $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new DraftModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑文稿失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建文稿失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param  $navid
     * @return 
     */
    public function del($ids){
        return DraftModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 前端展示文稿
     * @param  $id
     * @return 
     */
    public function doShow($id){
        $model = new DraftModel();
        return $model->save(['show_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消前端展示文稿
     * @param  $id
     * @return 
     */
    public function cancelShow($id){
        $model = new DraftModel();
        return $model->save(['show_time' => 0], ['id' => $id]);
    }
    /**
     * 推荐文稿
     * @param  $id
     * @return 
     */
    public function doRecomm($id){
        $model = new DraftModel();
        return $model->save(['recommend_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消推荐文稿
     * @param  $id
     * @return 
     */
    public function cancelRecomm($id){
        $model = new DraftModel();
        return $model->save(['recommend_time' => 0], ['id' => $id]);
    }
}

