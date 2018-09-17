<?php
/**
 * 负责人业务层
 */
namespace app\wx_h5\biz;

use app\common\model\ContactModel;

class ContactBiz{
    /**
     * 负责人列表
     * @param unknown $cond
     * @param int $page
     * @param int $pageSize
     * @return unknown
     */
    public function all(){
        return ContactModel::where([])->hidden(ContactModel::hiddenFields())->order('orderid desc')->select();
    }
}



