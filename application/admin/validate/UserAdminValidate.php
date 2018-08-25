<?php
namespace app\admin\validate;

use think\Validate;

class UserAdminValidate extends Validate{
    protected $rule = [
        'username' => 'require',
        'pass' => 'require',
        'pass2' => 'confirm:pass',
        'roleid' => 'number|require',
        'id' => 'require|number'
    ];
    protected $message = [
        'username.require' => '用户名不能为空',
        'pass.require' => '密码不能为空',
        'pass2.confirm' => '两次密码不一致',
        'roleid.number' => '用户角色ID不是数字',
        'roleid.require' => '用户角色ID不能为空',
        'id.require' => '用户ID不能为空',
        'id.number' => '用户ID必须为数字'
    ];
    protected $scene = [
        'create' => ['username', 'pass', 'roleid', 'pass2'],
        'edit' => ['username','roleid', 'id'],
    ];
}

