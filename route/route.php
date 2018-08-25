<?php

use app\http\middleware\AdminAuth;
use app\http\middleware\AdminNav;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
Route::get('/test/index', 'index/test');

Route::get('hello/:name', 'index/hello');

Route::group('admin', function(){
    Route::rule('index/index', 'admin/Index/index')->middleware(AdminNav::class, 'system-role-list');
    Route::group('useradmin', function(){
        Route::rule('create', 'admin/UserAdmin/create');
        Route::rule('createrole', 'admin/UserAdmin/createRole');
        Route::rule('createnav', 'admin/UserAdmin/createNav');
        Route::rule('noauth', 'admin/UserAdmin/noauth');
        Route::rule('list', 'admin/UserAdmin/list')->middleware(AdminNav::class, 'system-user-list');
        Route::get('add', 'admin/UserAdmin/add')->middleware(AdminNav::class, 'system-user-add');
        Route::post('add', 'admin/UserAdmin/add');
        Route::get('edit', 'admin/UserAdmin/edit')->middleware(AdminNav::class, 'system-user-edit');
        Route::post('edit', 'admin/UserAdmin/edit');
        Route::rule('editpwd', 'admin/UserAdmin/editPwd');
        Route::rule('rolelist', 'admin/UserAdmin/roleList')->middleware(AdminNav::class, 'system-role-list');
        Route::post('roleedit', 'admin/UserAdmin/roleEdit');
        Route::get('roleedit', 'admin/UserAdmin/roleEdit')->middleware(AdminNav::class, 'system-role-edit');
        Route::post('roleadd', 'admin/UserAdmin/roleAdd');
        Route::get('roleadd', 'admin/UserAdmin/roleAdd')->middleware(AdminNav::class, 'system-role-add');
        Route::post('roledel', 'admin/UserAdmin/roleDel');
        Route::rule('navlist', 'admin/UserAdmin/navList')->middleware(AdminNav::class, 'system-nav-list');
        Route::rule('getnav', 'admin/UserAdmin/getNav');
        Route::rule('addnav', 'admin/UserAdmin/addNav');
        Route::rule('editnav', 'admin/UserAdmin/editNav');
        Route::rule('delnav', 'admin/UserAdmin/delNav');
    });
    Route::group('media', function(){
        Route::rule('test', 'admin/Media/test');
        Route::rule('vodauth', 'admin/Media/vodAuth');
        Route::rule('refreshvodauth', 'admin/Media/refreshVodAuth');
        Route::rule('imgsts', 'admin/Media/imgSts');
    });
    Route::group('book', function(){
        Route::rule('list', 'admin/Book/list')->middleware(AdminNav::class, 'book-list');
        Route::rule('add', 'admin/Book/add')->middleware(AdminNav::class, 'book-add');
        Route::rule('edit', 'admin/Book/edit')->middleware(AdminNav::class, 'book-edit');
        Route::rule('del', 'admin/Book/del');
    });
})->middleware(AdminAuth::class);

Route::rule('admin/useradmin/login', 'admin/UserAdmin/login');
Route::rule('admin/useradmin/tncode', 'admin/UserAdmin/tncode');
Route::rule('admin/useradmin/checktncode', 'admin/UserAdmin/checkTncode');

return [

];
