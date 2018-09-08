<?php
namespace app\common\validate;

use think\Validate;

class UserValidate extends Validate{
    protected $rule = [
        'alias' => 'require',
        'name' => 'require',
        'price' => 'require|number'
    ];
    protected $message = [
        'alias.require' => '昵称不能为空'
    ];
    protected $scene = [
        'save' => ['alias'],
    ];
}

