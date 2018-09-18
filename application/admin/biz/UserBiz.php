<?php
/**
 * 用户业务层
 */
namespace app\admin\biz;

use app\common\model\UserModel;

class UserBiz{
    /**
     * 用户列表
     * @param unknown $cond
     * @param unknown $page
     * @param unknown $pageSize
     * @return unknown[]
     */
    public function list($cond, $page, $pageSize){
        $users = UserModel::where($cond)->order('create_time desc')->page($page, $pageSize)->select();
        $count = UserModel::where($cond)->count('id');
        return ['list' => $users, 'count' => $count];
    }
}

