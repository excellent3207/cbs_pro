<?php
/**
 * 图书
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\admin\biz\ContactBiz;
use app\common\validate\ContactValidate;

class Contact{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 书籍列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['contactList' => \think\facade\Request::url()], true);
        $biz = new ContactBiz();
        $list = $biz->all();
        return view('', ['list' => $list]);
    }
    /**
     * 编辑
     * @return \think\response\Redirect
     */
    public function save(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new ContactValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new ContactBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('contact/save')->with('contact_save_data', $data);
            }else{
                return redirect(getPageHistory('contactList'));
            }
        }
        $biz = new ContactBiz();
        $id = $this->request->get('id');
        $contact = session('contact_save_data');
        if(empty($contact) && $id){
            $contact = $biz->get($id);
        }
        return view('', ['data' => $contact, 'prePage' => getPageHistory('contactList')]);
    }
    /**
     * 删除
     * @return \think\response\Json
     */
    public function del(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new ContactBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 编辑排序
     * @return \think\response\Json
     */
    public function editOrder(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $orderid = $this->request->post('orderid');
        $biz = new ContactBiz();
        try{
            $ret['data'] = $biz->editOrder($id, $orderid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
