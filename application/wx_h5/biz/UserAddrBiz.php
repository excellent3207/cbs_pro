<?php
/**
 * 用户地址业务层
 */
namespace app\wx_h5\biz;

use app\common\model\UserAddrModel;

class UserAddrBiz{
    /**
     * 根据ID获取用户地址
     * @param unknown $id
     * @return unknown
     */
    public function get($id){
        $user = config('user');
        return UserAddrModel::where([['id', '=', $id], ['userid', '=', $user['userid']]])->hidden(UserAddrModel::hiddenFields())->find();
    }
    /**
     * 创建或编辑用户地址
     * @param unknown $data
     * @throws AppException
     * @return boolean|unknown
     */
    public function save($data){
        $model = new UserAddrModel();
        if(isset($data['id']) && $data['id']){
            $res = $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
            if(!$res) throw new AppException('编辑地址失败');
            return $res;
        }else{
            $res = $model->allowField(TRUE)->save($data);
            if(!$res) throw new AppException('创建地址失败');
            return $model->id;
        }
    }
}



