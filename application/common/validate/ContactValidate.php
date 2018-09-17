<?php
namespace app\common\validate;

use think\Validate;

class ContactValidate extends Validate{
    protected $rule = [
        'id' => 'require',
        'name' => 'require',
        'office_phone' => 'require',
        'phone' => 'require',
        'qq' => 'require',
        'addr' => 'require'
    ];
    protected $message = [
        'id.require' => '图书ID不能为空',
        'name.require' => '负责人姓名不能为空',
        'office_phone.require' => '办公室电话不能为空',
        'phone.require' => '个人电话不能为空',
        'qq.require' => 'qq不能为空',
        'addr.require' => '负责地不能为空',
    ];
    protected $scene = [
        'save' => ['name', 'office_phone','phone','qq','addr'],
    ];
}

