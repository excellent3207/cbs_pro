<?php
/**
 * banner
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

use think\Request;
use app\admin\biz\BannerBiz;
use app\common\Pagination;
use app\common\validate\BannerValidate;

class Banner{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
     * 书籍列表
     * @return \think\response\View
     */
    public function list(){
        setPageHistory(['bannerList' => \think\facade\Request::url()], true);
        $params = $this->request->get();
        $biz = new BannerBiz();
        $cond = [];
        if(isset($params['id']) && $params['id']){
            array_push($cond, ['id', '=', $params['id']]);
        }
        if(isset($params['is_show']) && $params['is_show'] != -1){
            $c = $params['is_show'] ? '<>' : '=';
            array_push($cond, ['show_time', $c, 0]);
        }
        $page = $this->request->get('page', 1);
        $pageSize = 10;
        $list = $biz->list($cond, $page, $pageSize);
        $pagination = new Pagination($page, $pageSize, $biz->listCount($cond));
        return view('', ['list' => $list, 'params' => $params, 'pagination' => $pagination]);
    }
    /**
     * 编辑用户
     * @return \think\response\Redirect
     */
    public function save(){
        $data = $this->request->post();
        if(!empty($data)){
            $validate = new BannerValidate();
            $error = '';
            if(!$validate->check($data, [], 'save')){
                $error = $validate->getError();
            }else{
                $biz = new BannerBiz();
                try{
                    $biz->save($data);
                }catch(\Exception $e){
                    $error = $e->getMessage();
                }
            }
            if($error){
                $data['error'] = $error;
                return redirect('banner/save')->with('banner_save_data', $data);
            }else{
                return redirect(getPageHistory('bannerList'));
            }
        }
        $biz = new BannerBiz();
        $id = $this->request->get('id');
        $banner = session('banner_save_data');
        if(empty($banner) && $id){
            $banner = $biz->get($id);
        }
        return view('', ['data' => $banner, 'prePage' => getPageHistory('bannerList')]);
    }
    /**
     * 删除导航
     * @return \think\response\Json
     */
    public function del(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $ids = $this->request->post('ids');
        $biz = new BannerBiz();
        try{
            $ret['data'] = $biz->del($ids);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 前端展示banner
     * @return \think\response\Json
     */
    public function doShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BannerBiz();
        try{
            $ret['data'] = $biz->doShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 取消前端展示banner
     * @return \think\response\Json
     */
    public function cancelShow(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $biz = new BannerBiz();
        try{
            $ret['data'] = $biz->cancelShow($id);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
    /**
     * 编辑分类排序
     * @return \think\response\Json
     */
    public function editOrder(){
        $ret = ['errorcode' => 0, 'msg' => '成功'];
        $id = $this->request->post('id');
        $orderid = $this->request->post('orderid');
        $biz = new BannerBiz();
        try{
            $ret['data'] = $biz->editOrder($id, $orderid);
        }catch(\Exception $e){
            $ret['errorcode'] = 1;
            $ret['msg'] = $e->getMessage();
        }
        return json($ret);
    }
}
