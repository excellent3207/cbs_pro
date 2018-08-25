<?php
/**
 * 
 * Author xjp
 * CreateTime 2018/08/24
 */
namespace app\admin\controller;

class Index{
    public function index(){
        dump(config('user'));
        return view('', ['user' => config('user')]);
    }
    public function login(){
        return 'login';
    }
}
