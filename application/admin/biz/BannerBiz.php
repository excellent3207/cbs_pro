<?php
/**
 * 后台banner业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BannerModel;

class BannerBiz{
    public function getRole(){
        
    }
    /**
     * 获取banner详情
     * @param $id
     * @return \app\common\model\BannerModel
     */
    public function get($id){
        $res = BannerModel::get($id)->hideField();
        return $res;
    }
    /**
     * banner列表
     * @param  $cond
     * @param int $page
     * @param int $pageSize
     * @return 
     */
    public function list($cond, int $page, int $pageSize){
        $drafts = BannerModel::where($cond)->hidden(BannerModel::hiddenFields())->page($page, $pageSize)->select();
        return $drafts;
    }
    /**
     * banner列表数量
     * @param  $cond
     * @return 
     */
    public function listCount($cond){
        return BannerModel::where($cond)->count('id');
    }
    /**
     * 编辑
     * @param  $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new BannerModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑banner失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建banner失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param  $navid
     * @return 
     */
    public function del($ids){
        return BannerModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 前端展示banner
     * @param  $id
     * @return 
     */
    public function doShow($id){
        $model = new BannerModel();
        return $model->save(['show_time' => $_SERVER['REQUEST_TIME']], ['id' => $id]);
    }
    /**
     * 取消前端展示banner
     * @param  $id
     * @return 
     */
    public function cancelShow($id){
        $model = new BannerModel();
        return $model->save(['show_time' => 0], ['id' => $id]);
    }
    /**
     * banner排序
     * @param  $id
     * @return 
     */
    public function editOrder($id, $orderid){
        $model = new BannerModel();
        return $model->save(['orderid' => $orderid], ['id' => $id]);
    }
}

