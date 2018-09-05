<?php
namespace app\http\middleware;

use think\Db;

class AdminAuth {
    public function handle($request, \Closure $next){
        /*Db::listen(function ($sql, $time, $explain, $master) {
            // 记录SQL
            echo $sql . ' [' . $time . 's] ' . ($master ? 'master' : 'slave');
            // 查看性能分析结果
            dump($explain);
        });*/
        $user = session('user');
        if(empty($user)){
            return redirect('useradmin/login');
        }
        config('user', $user);
        return $next($request);
    }
}

