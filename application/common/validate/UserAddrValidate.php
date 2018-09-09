<?php
namespace app\common\validate;

use think\Validate;

class UserAddrValidate extends Validate{
    protected $rule = [
        'userid' => 'require',
        'name' => 'require',
        'price' => 'require|number'
    ];
    protected $message = [
        'userid.require' => '用户不能为空',
        'name.require' => '姓名不能为空',
        'phone.require' => '手机号不能为空',
    ];
    protected $scene = [
        'save' => ['userid', 'name', 'phone'],
    ];
}

