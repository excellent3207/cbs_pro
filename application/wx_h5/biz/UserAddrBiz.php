<?php
/**
 * 用户地址业务层
 */
namespace app\wx_h5\biz;

use app\common\model\UserAddrModel;
use think\Db;

class UserAddrBiz{
    /**
     * 根据ID获取用户地址
     * @param unknown $id
     * @return unknown
     */
    public function get($id){
        $user = config('user');
        return UserAddrModel::where([['id', '=', $id], ['userid', '=', $user['id']]])->hidden(UserAddrModel::hiddenFields())->find();
    }
    /**
     * 我的所有地址
     * @return unknown
     */
    public function all(){
        $user = config('user');
        return UserAddrModel::where([['userid', '=', $user['id']]])->order('isdefault desc,create_time desc')->hidden(UserAddrModel::hiddenFields())->select();
    }
    /**
     * 删除地址
     * @param unknown $id
     * @return boolean
     */
    public function del($id){
        $user = config('user');
        return UserAddrModel::destroy(function($query) use ($id, $user){
            $query->where([['id','=', $id], ['userid', '=', $user['id']]]);
        });
    }
    /**
     * 创建或编辑用户地址
     * @param unknown $data
     * @throws AppException
     * @return boolean|unknown
     */
    public function save($data){
        $model = new UserAddrModel();
        Db::startTrans();
        try{
            if(isset($data['id']) && $data['id']){
                $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
                if(!$res) throw new AppException('编辑地址失败');
            }else{
                $res = $model->allowField(TRUE)->save($data);
                if(!$res) throw new AppException('创建地址失败');
                $res = $model->id;
            }
            if(isset($data['isdefault']) && $data['isdefault']){
                UserAddrModel::update(['isdefault' => 0], [['id', '<>', $model->id]]);
            }
            Db::commit();
            return $res;
        }catch(\Exception $e){
            Db::rollback();
            throw $e;
        }
    }
}



