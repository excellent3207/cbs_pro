<?php
/**
 * 联系方式业务层
 */
namespace app\admin\biz;

use app\common\exception\AppException;
use app\common\model\BookModel;
use app\common\model\ContactModel;

class ContactBiz{
    public function getRole(){
        
    }
    /**
     * 获取联系方式详情
     * @param unknown $id
     * @return \app\common\model\BookModel
     */
    public function get($id){
        $res = ContactModel::get($id)->hideField();
        return $res;
    }
    /**
     * 联系方式列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function all(){
        return ContactModel::all()->hidden(BookModel::hiddenFields());
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function save($data){
        $model = new ContactModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑联系方式失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建联系方式失败');
            return $model->id;
        }
    }
    /**
     * 删除
     * @param unknown $navid
     * @return unknown
     */
    public function del($ids){
        return ContactModel::destroy(function($query) use ($ids){
            $query->where('id','in', $ids);
        });
    }
    /**
     * 排序
     * @param unknown $id
     * @return unknown
     */
    public function editOrder($id, $orderid){
        $model = new ContactModel();
        return $model->save(['orderid' => $orderid], ['id' => $id]);
    }
}

