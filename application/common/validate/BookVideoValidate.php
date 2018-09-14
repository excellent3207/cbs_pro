<?php
namespace app\common\validate;

use think\Validate;

class BookVideoValidate extends Validate{
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'ali_vid' => 'require'
    ];
    protected $message = [
        'id.require' => '图书ID不能为空',
        'title.require' => '视频名称不能为空',
        'ali_vid.require' => '视频VID不能为空',
    ];
    protected $scene = [
        'save' => ['title', 'ali_vid'],
    ];
}

