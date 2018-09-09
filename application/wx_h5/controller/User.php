<?php
/**
 * 用户
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\wx_h5\controller;

use think\Request;
use app\wx_h5\biz\UserBiz;
use app\common\validate\UserValidate;
use app\common\validate\UserAddrValidate;
use app\wx_h5\biz\UserAddrBiz;

class User{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 我的
     */
    public function index(){
        return view('', ['user' => config('user')]);
    }
    /**
     * 我的书架
     * @return \think\response\View
     */
    public function shelf(){
        return view('', []);
    }
    /**
     * 我的图书
     * @return \think\response\View
     */
    public function myBooks(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        try{
            $biz = new UserBiz();
            $cond = [];
            $order = [];
            $page = $this->request->get('page', 1);
            $pageSize = 12;
            $name = $this->request->get('name');
            if($name){
                array_push($cond, ['name', 'like', $name.'%']);
            }
            $ret['data'] = $biz->myBooks($cond, $order, $page, $pageSize);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 用户信息
     */
    public function info(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new UserValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new UserBiz();
                try{
                    $user = config('user');
                    $data['id'] = $user['id'];
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('user/info')->with('user_save_data', $data);
            }else{
                return redirect('user/index');
            }
        }
        $user = config('user');
        $newUser = session('user_save_data');
        if(empty($newUser)){
            $newUser = $user;
        }else{
            $newUser['id'] = $user['id'];
        }
        return view('', ['user' => $newUser]);
    }
    /**
     * 约稿函
     */
    public function draftLetter(){
        return view('', []);
    }
    /**
     * 联系我们
     */
    public function contact(){
        return view('', []);
    }
    /**
     * 地址管理
     */
    public function addr(){
        return view('', []);
    }
    /**
     * 创建/编辑地址管理
     */
    public function addrSave(){
        $data = $this->request->post();
        if(!empty($data)){
            $user = config('user');
            $data['userid'] = $user['id'];
            $validate = new UserAddrValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new UserAddrBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('user/addradd')->with('useraddr_save_data', $data);
            }else{
                return redirect('user/addr');
            }
        }
        $biz = new UserAddrBiz();
        $id = $this->request->get('id');
        $addr = session('useraddr_save_data');
        if(empty($addr) && $id){
            $addr = $biz->get($id);
        }
        return view('', ['addr' => $addr]);
    }
}

