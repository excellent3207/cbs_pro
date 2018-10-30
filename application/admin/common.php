<?php
/**
 * 判断权限
 * @param $tag
 */
function checkAuth($tag){
    $user = config('user');
    $navTag = $user['nav']['dataTag'];
    return !empty($navTag[$tag]);
}/**
* 设置历史页面
* @param array $datas
*/
function setPageHistory(array $datas, bool $clear = false){
    if($clear){
        $history = $datas;
    }else{
        $history = cookie('history');
        if($history){
            $history = unserialize($history);
        }else{
            $history = [];
        }
        $history = array_merge($history, $datas);
    }
    cookie('history', serialize($history), 86400);
}
/**
 * 删除历史页面
 * @param array $keys
 */
function delPageHistory(array $keys){
    $history = cookie('history');
    if($history){
        $history = unserialize($history);
        foreach($keys as $key){
            if(isset($history[$key]))
                unset($history[$key]);
        }
    }else{
        $history = [];
    }
    cookie('history', serialize($history), 86400);
}
/**
 * 获取历史页面
 * @param string $key
 * @return string
 */
function getPageHistory(string $key, string $default = ''){
    $history = cookie('history');
    if($history){
        $history = unserialize($history);
        return isset($history[$key]) ? $history[$key] : $default;
    }else{
        return $default;
    }
}