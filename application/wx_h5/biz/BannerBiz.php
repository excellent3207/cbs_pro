<?php
/**
 * banner务层
 */
namespace app\wx_h5\biz;

use app\common\model\BannerModel;

class BannerBiz{
    /**
     * banner列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function all(){
        return BannerModel::where([['show_time', '<>', 0]])->order('orderid desc')->hidden(BannerModel::hiddenFields())->select();
    }
}