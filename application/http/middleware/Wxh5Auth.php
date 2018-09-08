<?php
namespace app\http\middleware;

use think\Db;
use app\common\model\UserModel;

class Wxh5Auth {
    public function handle($request, \Closure $next){
        /*Db::listen(function ($sql, $time, $explain, $master) {
        // 记录SQL
        echo $sql . ' [' . $time . 's] ' . ($master ? 'master' : 'slave');
        // 查看性能分析结果
        dump($explain);
        });*/
        $user = UserModel::get(1);
        $user = serialize($user);
        //$user = session('user');
        config('user', unserialize($user));
        return $next($request);
    }
}

