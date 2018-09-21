<?php
namespace app\http\middleware;

use think\Db;
use app\common\model\UserModel;
use app\common\biz\CbsWxBiz;

class Wxh5Auth {
    public function handle($request, \Closure $next){
        /*Db::listen(function ($sql, $time, $explain, $master) {
        // 记录SQL
        echo $sql . ' [' . $time . 's] ' . ($master ? 'master' : 'slave');
        // 查看性能分析结果
        dump($explain);
        });*/
        $wxBiz = new CbsWxBiz();
        $user = UserModel::get(1);
        config('user', $user);
        /*$code = $request->get('code');
        $user = session('user');
        $url = 'http://h5.igniter.vip'.$request->url();
        if(empty($user)){
            $res = $wxBiz->authLogin($url, $code);
            switch($res['action']){
                case 'redirect':
                    header('Location:'.$res['data']);exit;
                    break;
                case 'info':
                    $userModel = new UserModel();
                    $openid = $res['data']['openid'];
                    $user = $userModel->get(['wx_openid' => $openid]);
                    if(empty($user)){
                        $data = ['wx_openid' => $openid, 'issubscribe' => $res['data']['issubscribe'] ? 1 : 0];
                        $res = $userModel->save($data);
                        if(!($res)) throw new \Exception('创建用户失败');
                        $user = $userModel->get($userModel->id);
                    }
                    session('user', serialize($user));
                    config('user', $user);
            }
        }else{
            config('user', unserialize($user));
        }*/
        config('wxConfig', $wxBiz->createJsConfig($url));
        return $next($request);
    }
}

