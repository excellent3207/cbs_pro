<?php
use app\common\AppTool;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function formatUrl($uri){
    return AppTool::formatUrl($uri);
}
function formatPrice($price){
    if($price%100 !== 0){
        return sprintf("%.2f",$price/100);
    }else{
        return intval($price/100).'';
    }
}