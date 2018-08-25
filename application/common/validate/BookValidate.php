<?php
namespace app\common\validate;

use think\Validate;

class BookValidate extends Validate{
    protected $rule = [
        'id' => 'require',
    ];
    protected $message = [
        'id.require' => '图书ID不能为空',
    ];
    protected $scene = [
        'create' => [],
        'edit' => ['id'],
    ];
}

