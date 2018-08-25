<?php
/**
 * 后台用户业务层
 */
namespace app\admin\biz;

use app\admin\model\UserAdminModel;
use app\common\exception\AppException;
use app\admin\model\NavAdminModel;

class UserAdminBiz{
    /**
     * 登录
     * @param string $username
     * @param string $pass
     * @throws AppException
     * @return boolean
     */
    public function doLogin($data){
        if(!isset($data['username']) || !$data['username']) throw new AppException('用户名不能为空');
        if(!isset($data['pass']) || !$data['pass']) throw new AppException('密码不能为空');
        $user = UserAdminModel::get(['username' => $data['username']])->hideField();
        $user['role'] = $user->role->hideField();
        $user['nav'] = $this->formatNavs($user['role']);
        if(empty($user)) throw new AppException('用户不存在');
        if($user['pass'] != md5($data['pass'])) throw new AppException('用户名或密码错误');
        UserAdminModel::where('id', $user['id'])->update(['logintime' => $_SERVER['REQUEST_TIME']]);
        $user['logintime'] = $_SERVER['REQUEST_TIME'];
        session('user', $user);
        return true;
    }
    /**
     * 退出登录
     */
    public function logout(){
        session('user', null);
    }
    /**
     * 修改密码
     * @param Array $data
     * @throws AppException
     * @return boolean
     */
    public function editPwd($data){
        $user = config('user');
        $curUser = $this->get($user['id']);
        if(empty($curUser)) throw new AppException('用户不存在');
        if(!isset($data['oldPwd']) || !$data['oldPwd']) throw new AppException('原始密码不能为空');
        if(!isset($data['pwd1']) || !$data['pwd1']) throw new AppException('新密码不能为空');
        $oldPwdMd5 = md5($data['oldPwd']);
        if($oldPwdMd5 != $curUser['pass']) throw new AppException('原始密码错误');
        if(!isset($data['pwd2']) || $data['pwd1'] != $data['pwd2']) throw new AppException('新密码两次输入有误');
        $this->checkPassword($data['pwd1']);
        $model = new UserAdminModel();
        $res = $model->save(['pass' => $data['pwd1']], ['id' => $curUser['id']]);
        if(!$res) throw new AppException('修改密码失败');
        return true;
    }
    /**
     * 检查密码格式
     */
    private function checkPassword(string $pwd){
        if(strlen($pwd) < 10) throw new AppException('密码长度不能小于10');
        if(!preg_match('/[0-9]/', $pwd) || !preg_match('/[A-Z]/', $pwd) || !preg_match('/[a-z]/', $pwd)){
            throw new AppException('密码必须包含大写字符，小写字母，数字');
        }
    }
    /**
     * 获取用户
     * @param unknown $id
     * @return \app\admin\model\UserAdminModel|NULL
     */
    public function get($id){
        $user = UserAdminModel::get($id);
        return $user;
    }
    /**
     * 后台用户列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function list($cond, int $page, int $pageSize){
        $users = UserAdminModel::with('role')->where($cond)->hidden(UserAdminModel::hiddenFields())->page($page, $pageSize)->select();
        foreach($users as &$user){
            $user['role'] = $user->role->hideField();
        }
        return $users;
    }
    /**
     * 用户列表数量
     * @param unknown $cond
     * @return unknown
     */
    public function listCount($cond){
        return UserAdminModel::where($cond)->count('id');
    }
    /**
     * 创建用户
     * @param unknown $data
     * @throws AppException
     * @return unknown
     */
    public function add($data){
        $count = UserAdminModel::where('username', $data['username'])->count('id');
        if($count > 0) throw new AppException('用户名已经被占用');
        $model = new UserAdminModel();
        $res = $model->allowField(TRUE)->save($data);
        if(!$res) throw new AppException('创建后台用户失败');
        return $model->id;
    }
    /**
     * 编辑用户
     * @param unknown $data
     * @throws AppException
     */
    public function edit($data){
        $count = UserAdminModel::where('username', $data['username'])->count('id');
        if($count > 0) throw new AppException('用户名已经被占用');
        $model = new UserAdminModel();
        return $model->allowField(TRUE)->save($data, ['id' => $data['id']]);
    }
    /**
     * 格式化导航数据
     * @param unknown $role
     * @return array[]|unknown[][]|array
     */
    private function formatNavs($role){
        if(!empty($role) && $role['navids']){
            $navs = NavAdminModel::where([['navid', 'in', $role['navids']]])
                ->hidden(NavAdminModel::hiddenFields())->order('orderid', 'desc')->select();
            $data = [];
            $dataTag = [];
            foreach($navs as $v){
                if(!isset($data[$v['level']])){
                    $data[$v['level']] = [];
                }
                if(!isset($data[$v['level']][$v['parentid']])){
                    $data[$v['level']][$v['parentid']] = [];
                }
                array_push($data[$v['level']][$v['parentid']], $v);
                $dataTag[$v['tag']] = $v;
            }
            return ['data' => $data, 'dataTag' => $dataTag];
        }
        return [];
    }
}

