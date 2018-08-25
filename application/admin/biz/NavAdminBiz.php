<?php
/**
 * 后台页面导航业务层
 */
namespace app\admin\biz;

use app\admin\model\NavAdminModel;
use app\common\exception\AppException;

class NavAdminBiz{
    public function getRole(){
        
    }
    public function all(){
        return NavAdminModel::order('navid desc')->hidden(NavAdminModel::hiddenFields())->select();
    }
    public function get($navid){
        return NavAdminModel::get($navid)->hideField();
    }
    /**
     * 创建导航
     * @param unknown $data
     * @throws AppException
     * @return unknown
     */
    public function add($data){
        if(!isset($data['tag']) || !$data['tag']) throw new AppException('导航唯一标示(tag)不能为空');
        if(!isset($data['parentid'])) throw new AppException('父级ID不能为空');
        if($data['parentid']){
            $parent = NavAdminModel::get($data['parentid']);
            if(empty($parent)) throw new AppException('父级导航不存在');
            $data['level'] = $parent['level']+1;
        }else{
            $data['level'] = 1;
        }
        $count = NavAdminModel::where('tag', $data['tag'])->count('navid');
        if($count > 0) throw new AppException('tag已经存在');
        $model = new NavAdminModel();
        $res = $model->allowField(TRUE)->save($data);
        if(!$res) throw new AppException('创建导航失败');
        return $model->navid;
    }
    /**
     * 编辑
     * @param unknown $data
     * @throws AppException
     * @return boolean
     */
    public function edit($data){
        if(!isset($data['navid']) || !$data['navid']) throw new AppException('导航ID不能为空');
        if(!isset($data['tag']) || !$data['tag']) throw new AppException('导航唯一标示(tag)不能为空');
        $nav = NavAdminModel::where('tag', $data['tag'])->find();
        if(!empty($nav) && $nav['navid'] != $data['navid']) throw new AppException('tag已经存在');
        $model = new NavAdminModel();
        return $model->allowField(TRUE)->save($data, ['navid' => $data['navid']]);
    }
    /**
     * 删除
     * @param unknown $navid
     * @return unknown
     */
    public function del($navid){
        return NavAdminModel::destroy(function($query) use ($navid){
            $query->where('navid', $navid);
        });
    }
}

