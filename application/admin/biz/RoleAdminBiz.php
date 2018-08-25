<?php
/**
 * 后台用户角色业务层
 */
namespace app\admin\biz;

use app\admin\model\RoleAdminModel;
use app\common\exception\AppException;

class RoleAdminBiz{
    /**
     * 根据ID获取角色
     * @param unknown $id
     * @return \app\admin\model\RoleAdminModel
     */
    public function get($id){
        return RoleAdminModel::get($id)->hideField();
    }
    /**
     * 角色列表（全部）
     * @return unknown
     */
    public function all(){
        $hiddens = array_merge(RoleAdminModel::hiddenFields(), ['navids']);
        return RoleAdminModel::order('id desc')->hidden($hiddens)->select();
    }
    /**
     * 编辑角色
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function edit($data){
        if(!isset($data['id']) || !$data['id']) throw new AppException('角色ID不能为空');
        $model = new RoleAdminModel();
        return $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
    }
    /**
     * 创建角色
     * @param unknown $data
     * @throws AppException
     * @return unknown
     */
    public function add($data){
        $model = new RoleAdminModel();
        $res = $model->allowField(TRUE)->save($data);
        if(!$res) throw new AppException('创建角色失败');
        return $model->id;
    }
    /**
     * 删除角色
     * @param unknown $ids
     * @throws AppException
     * @return boolean
     */
    public function del($ids){
        if(!$ids) throw new AppException('请选择要删除的角色');
        return RoleAdminModel::destroy($ids);
    }
}

