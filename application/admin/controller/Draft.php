<?php
/**
 * 文稿
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\admin\biz\DraftBiz;
use app\common\validate\DraftValidate;
use app\common\Pagination;

class Draft{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 书籍列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['draftList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new DraftBiz();
        $cond = [];
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $page, $pageSize);
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond));
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination]);
    }
    /**
     * 编辑用户
     * @return \think\response\Redirect|unknown
     */
    public function save(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new DraftValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new DraftBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('draft/save')->with('draft_save_data', $data);
            }else{
                return redirect(getPageHistory('draftList'));
            }
        }
        $biz = new DraftBiz();
        $id = $this->request->get('id');
        $draft = session('draft_save_data');
        if(empty($draft) && $id){
            $draft = $biz->get($id);
        }
        return view('', ['data' => $draft, 'prePage' => getPageHistory('draftList')]);
    }
    /**
     * 删除导航
     * @return \think\response\Json
     */
    public function del(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new DraftBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 前端展示文稿
     * @return \think\response\Json
     */
    public function doShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new DraftBiz();
        try{
            $ret['data'] = $biz->doShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消前端展示文稿
     * @return \think\response\Json
     */
    public function cancelShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new DraftBiz();
        try{
            $ret['data'] = $biz->cancelShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 推荐文稿
     * @return \think\response\Json
     */
    public function doRecomm(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new DraftBiz();
        try{
            $ret['data'] = $biz->doRecomm($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消推荐文稿
     * @return \think\response\Json
     */
    public function cancelRecomm(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new DraftBiz();
        try{
            $ret['data'] = $biz->cancelRecomm($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
