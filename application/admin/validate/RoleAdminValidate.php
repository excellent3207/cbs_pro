<?php
namespace app\admin\validate;

use think\Validate;

class RoleAdminValidate extends Validate{
    protected $rule = [
        'name' => 'require',
    ];
    protected $message = [
        'name.require' => '角色名不能为空',
    ];
    protected $scene = [
        'create' => ['name'],
    ];
}

