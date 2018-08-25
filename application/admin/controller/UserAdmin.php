<?php
/**
 * 后台用户
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use app\admin\biz\TnCode;
use think\Request;
use app\admin\biz\UserAdminBiz;
use app\admin\validate\UserAdminValidate;
use app\admin\validate\RoleAdminValidate;
use app\admin\biz\RoleAdminBiz;
use app\common\Pagination;
use app\admin\biz\NavAdminBiz;

class UserAdmin{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    public function index(){
        return view('', []);
    }
    /**
     * 登录
     * @return \think\response\View
     */
    public function login(){
        $data = $this->request->post();
        if(!empty($data)){
            try{
                $biz = new UserAdminBiz();
                $biz->doLogin($data);
                return redirect('index/index');
            }catch(\Exception $e){
                $data['error'] = $e->getMessage();
                return redirect('useradmin/login')->with('login_data', $data);
            }
        }
        
        $loginData = session('login_data');
        return view('', ['loginData' => $loginData]);
    }
    /**
     * 登出
     * @return \think\response\Redirect
     */
    public function logout(){
        $biz = new UserAdminBiz();
        $biz->logout();
        return redirect('useradmin/login');
    }
    /**
     * 修改密码
     * @return \think\response\Json
     */
    public function editPwdJson(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $data = $this->request->post();
        $biz = new UserAdminBiz();
        try{
            $ret['data'] = $biz->editPwd($data);
            $biz->logout();
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 图片验证码
     */
    public function tncode(){
        $tn  = new TnCode();
        $tn->make();
    }
    /**
     * 图片验证码
     */
    public function checkTncode(){
        $tn  = new TnCode();
        if($tn->check()){
            $_SESSION['tncode_check'] = 'ok';
            echo "ok";
        }else{
            $_SESSION['tncode_check'] = 'error';
            echo "error";
        }
    }
    /**
     * 未登录
     */
    public function noauth(){
        return view('', []);
    }
    /**
     * 用户列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['userList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new UserAdminBiz();
        $cond = [];
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['roleid']) && $params['roleid']){
            array_push($cond, ['roleid', '=', $params['roleid']]);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $page, $pageSize);
        $roleBiz = new RoleAdminBiz();
        $roles = $roleBiz->all();
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond));
        return view('', ['list' => $list, 'roles' => $roles, 'params' => $params, 'pagination' => $pagination]);
    }
    /**
     * 创建用户
     * @return \think\response\Redirect|unknown
     */
    public function add(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new UserAdminValidate();
            $error = '';
            if(!$validate->check($data, [], 'create')){
                $error = $validate->getError();
            }else{
                $biz = new UserAdminBiz();
                try{
                    $biz->add($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('useradmin/add')->with('user_add_data', $data);
            }else{
                return redirect(getPageHistory('userList'));
            }
        }
        $user = session('user_add_data');
        if(empty($user)) $user = [];
        $roleBiz = new RoleAdminBiz();
        $roleList = $roleBiz->all();
        return view('', ['data' => $user, 'roleList' => $roleList, 'prePage' => getPageHistory('userList')]);
    }
    /**
     * 编辑用户
     * @return \think\response\Redirect|unknown
     */
    public function edit(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new UserAdminValidate();
            $error = '';
            if(!$validate->check($data, [], 'edit')){
                $error = $validate->getError();
            }else{
                $biz = new UserAdminBiz();
                try{
                    $biz->edit($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('useradmin/edit')->with('user_edit_data', $data);
            }else{
                return redirect(getPageHistory('userList'));
            }
        }
        $biz = new UserAdminBiz();
        $id = $this->request->get('id');
        $user = session('user_edit_data');
        if(empty($user)){
            $user = $biz->get($id);
        }
        $roleBiz = new RoleAdminBiz();
        $roleList = $roleBiz->all();
        return view('', ['data' => $user, 'roleList' => $roleList, 'prePage' => getPageHistory('userList')]);
    }
    /**
     * 角色列表
     * @return \think\response\View
     */
    public function roleList(){
        setPageHistory(['roleList' => \think\facade\Request::url()], true);
        $biz = new RoleAdminBiz();
        $list = $biz->all();
        return view('', ['list' => $list]);
    }
    /**
     * 角色编辑
     * @return \think\response\Redirect|unknown
     */
    public function roleEdit(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new RoleAdminValidate();
            $error = '';
            if(!$validate->check($data, [], 'create')){
                $error = $validate->getError();
            }else{
                $biz = new RoleAdminBiz();
                try{
                    $biz->edit($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('useradmin/roleedit')->params(['id' => $data['id']])->with('role_edit_data', $data);
            }else{
                return redirect(getPageHistory('roleList'));
            }
        }
        $role = session('role_edit_data');
        if(empty($role)){
            $id = $this->request->get('id', 2);
            $roleBiz = new RoleAdminBiz();
            $role = $roleBiz->get($id);
        }
        $navBiz = new NavAdminBiz();
        $navList = $navBiz->all();
        return view('', ['data' => $role, 'navlist' => $navList, 'prePage' => getPageHistory('roleList')]);
    }
    /**
     * 创建角色
     * @return \think\response\Redirect|unknown
     */
    public function roleAdd(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new RoleAdminValidate();
            $error = '';
            if(!$validate->check($data, [], 'create')){
                $error = $validate->getError();
            }else{
                $biz = new RoleAdminBiz();
                try{
                    $biz->add($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('useradmin/roleadd')->with('role_add_data', $data);
            }else{
                return redirect(getPageHistory('roleList'));
            }
        }
        $role = session('role_add_data');
        $navBiz = new NavAdminBiz();
        $navList = $navBiz->all();
        return view('', ['data' => $role, 'navlist' => $navList, 'prePage' => getPageHistory('roleList')]);
    }
    /**
     * 删除角色
     * @return \think\response\Json
     */
    public function roleDel(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new RoleAdminBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 导航列表
     * @return \think\response\View
     */
    public function navList(){
        $biz = new NavAdminBiz();
        $list = $biz->all();
        return view('', ['list' => $list]);
    }
    public function getNavJson(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $navid = $this->request->get('navid');
        $biz = new NavAdminBiz();
        $ret['data'] = $biz->get($navid);
        return json($ret);
    }
    /**
     * 添加导航
     * @return \think\response\Json
     */
    public function addNav(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $data = $this->request->post();
        $biz = new NavAdminBiz();
        try{
            $ret['data'] = $biz->add($data);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 编辑导航
     * @return \think\response\Json
     */
    public function editNav(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $data = $this->request->post();
        $biz = new NavAdminBiz();
        try{
            $ret['data'] = $biz->edit($data);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 删除导航
     * @return \think\response\Json
     */
    public function delNav(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $navid = $this->request->post('navid');
        $biz = new NavAdminBiz();
        try{
            $ret['data'] = $biz->del($navid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
