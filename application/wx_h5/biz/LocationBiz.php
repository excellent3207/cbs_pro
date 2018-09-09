<?php
/**
 * 地址业务层
 */
namespace app\wx_h5\biz;

use app\common\model\LocationModel;

class LocationBiz{
    /**
     * 所有省份列表
     * @return unknown
     */
    public function provinces(){
        return LocationModel::where('parentid', 8)->select();
    }
    /**
     * 省份下城市列表
     * @param unknown $provinceid
     */
    public function citys($provinceid){
        return LocationModel::where('parentid', $provinceid)->select();
    }
    /**
     * 城市下县城列表
     * @param unknown $cityid
     */
    public function countys($cityid){
        return LocationModel::where('parentid', $cityid)->select();
    }
}
