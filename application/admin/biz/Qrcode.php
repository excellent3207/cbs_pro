<?php
/**
 * 二维码业务层
 */
namespace app\admin\biz;

class Qrcode{
    /**
    * 根据地址生成二维码并下载
    * @param int $userid
    * @return string[]|boolean[]|mixed[]
    */
    public function download($url){
        include env('app_path').'common/qrcode/phpqrcode.php';
        $name = 'qrcode_'.date('YmdHis').'.png';
        header('Content-Disposition:attachment;filename='.$name);
        //容错级别
        $errorCorrenctionLevel = 'l';
        //二维码图片大小
        $matrixPointSize = 6;
        //生成二维码
        echo \QRcode::png($url,false,$errorCorrenctionLevel,$matrixPointSize,2);exit;
    }
}

