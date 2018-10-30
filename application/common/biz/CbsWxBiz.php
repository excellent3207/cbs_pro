<?php 
/**
 * 微信业务层
 * author：xjp
 * create：2017.4.8
 */
namespace app\common\biz;

use app\common\model\CbsWxModel;

class CbsWxBiz{
    /**
     * 获取js配置
     * @param  $url
     */
    public function createJsConfig($url){
        $model = new CbsWxModel();
        return $model->createJsConfig($url);
    }
    /**
     * 微信认证
     * @param  $url
     * @param  $code
     * @return string[]|number[]|boolean[]|mixed[]
     */
    public function authLogin($url, $code){
        $model = new CbsWxModel();
        return $model->authLogin($url, $code);
    }
}
?>