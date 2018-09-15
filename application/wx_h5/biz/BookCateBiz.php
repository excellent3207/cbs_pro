<?php
/**
 * 图书分类业务层
 */
namespace app\wx_h5\biz;

use app\common\model\BookCateModel;

class BookCateBiz{
    /**
     * 图书列表
     * @param unknown $type
     * @return unknown
     */
    public function listByType($type){
        $cond = [];
        if($type){
            array_push($cond, ['type', 'in', [$type, 3]]);
        }
        $res = BookCateModel::where($cond)->order('orderid desc')->hidden(BookCateModel::hiddenFields())->select();
        $data = ['gaozhi' => [], 'benke' => []];
        foreach($res as $v){
            if($v['type'] == 1){
                array_push($data['benke'], $v);
            }else{
                
            }
            switch($v['type']){
                case 1:
                    array_push($data['benke'], $v);
                    break;
                case 2:
                    array_push($data['gaozhi'], $v);
                    break;
                case 3:
                    if($type == 1 || $type == 0){
                        array_push($data['benke'], $v);
                    }
                    if($type == 2 || $type == 0){
                        array_push($data['gaozhi'], $v);
                    }
                    break;
            }
        }
        return $data;
    }
}



