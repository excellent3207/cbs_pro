<?php
namespace app\common\validate;

use think\Validate;

class BannerValidate extends Validate{
    protected $rule = [
        'img' => 'require',
        'url' => 'require'
    ];
    protected $message = [
        'img.require' => '图片不能为空',
        'url.require' => '跳转地址不能为空',
    ];
    protected $scene = [
        'save' => ['img', 'url'],
    ];
}

