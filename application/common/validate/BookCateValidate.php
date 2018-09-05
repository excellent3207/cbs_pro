<?php
namespace app\common\validate;

use think\Validate;

class BookCateValidate extends Validate{
    protected $rule = [
        'id' => 'require',
        'catename' => 'require'
    ];
    protected $message = [
        'id.require' => '图书分类ID不能为空',
        'catename.require' => '图书分类名称不能为空',
    ];
    protected $scene = [
        'save' => ['catename'],
    ];
}

