<?php
namespace app\common\exception;

class AppException extends \Exception{
    const BUSINESS = 1;//业务异常
    const NO_LOGIN = 2;//未登录
    const NO_AUTH = 3; //没有权限
}

