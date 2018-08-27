<?php
namespace app\common\validate;

use think\Validate;

class BookValidate extends Validate{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'price' => 'require|number'
    ];
    protected $message = [
        'id.require' => '图书ID不能为空',
        'name.require' => '书名不能为空',
        'price.require' => '价格不能为空',
        'price.number' => '价格必须为数字'
    ];
    protected $scene = [
        'save' => ['name', 'price'],
    ];
}

