<?php

use app\http\middleware\AdminAuth;
use app\http\middleware\AdminNav;
use app\http\middleware\Wxh5Auth;

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
        Route::post('create', 'admin/UserAdmin/create');
        Route::post('createrole', 'admin/UserAdmin/createRole');
        Route::post('createnav', 'admin/UserAdmin/createNav');
        Route::get('noauth', 'admin/UserAdmin/noauth');
        Route::get('list', 'admin/UserAdmin/list')->middleware(AdminNav::class, 'system-user-list');
        Route::get('add', 'admin/UserAdmin/add')->middleware(AdminNav::class, 'system-user-add');
        Route::post('add', 'admin/UserAdmin/add');
        Route::get('edit', 'admin/UserAdmin/edit')->middleware(AdminNav::class, 'system-user-edit');
        Route::post('edit', 'admin/UserAdmin/edit');
        Route::post('del', 'admin/UserAdmin/del');
        Route::post('resetpwd', 'admin/UserAdmin/resetPwd');
        Route::post('editpwd', 'admin/UserAdmin/editPwd');
        Route::get('rolelist', 'admin/UserAdmin/roleList')->middleware(AdminNav::class, 'system-role-list');
        Route::post('roleedit', 'admin/UserAdmin/roleEdit');
        Route::get('roleedit', 'admin/UserAdmin/roleEdit')->middleware(AdminNav::class, 'system-role-edit');
        Route::post('roleadd', 'admin/UserAdmin/roleAdd');
        Route::get('roleadd', 'admin/UserAdmin/roleAdd')->middleware(AdminNav::class, 'system-role-add');
        Route::post('roledel', 'admin/UserAdmin/roleDel');
        Route::get('navlist', 'admin/UserAdmin/navList')->middleware(AdminNav::class, 'system-nav-list');
        Route::get('getnav', 'admin/UserAdmin/getNav');
        Route::post('addnav', 'admin/UserAdmin/addNav');
        Route::post('editnav', 'admin/UserAdmin/editNav');
        Route::post('delnav', 'admin/UserAdmin/delNav');
        Route::get('logout', 'admin/UserAdmin/logout');
    });
    Route::group('user', function(){
        Route::get('list', 'admin/User/list')->middleware(AdminNav::class, 'app-user-list');
    });
    Route::group('media', function(){
        Route::get('test', 'admin/Media/test');
        Route::post('vodauth', 'admin/Media/vodAuth');
        Route::post('refreshvodauth', 'admin/Media/refreshVodAuth');
        Route::post('imgsts', 'admin/Media/imgSts');
        Route::post('filests', 'admin/Media/fileSts');
        Route::post('editorupload', 'admin/Media/editorUpload');
    });
    Route::group('book', function(){
        Route::get('list', 'admin/Book/list')->middleware(AdminNav::class, 'book-list');
        Route::get('save', 'admin/Book/save')->middleware(AdminNav::class, 'book-save');
        Route::post('save', 'admin/Book/save');
        Route::post('del', 'admin/Book/del');
        Route::post('doshow', 'admin/Book/doShow');
        Route::post('cancelshow', 'admin/Book/cancelShow');
        Route::post('dorecomm', 'admin/Book/doRecomm');
        Route::post('cancelrecomm', 'admin/Book/cancelRecomm');
        Route::post('addcate', 'admin/Book/addCate');
        Route::get('videos', 'admin/Book/videos')->middleware(AdminNav::class, 'book-video-list');
        Route::get('savevideo', 'admin/Book/saveVideo')->middleware(AdminNav::class, 'book-video-save');
        Route::post('savevideo', 'admin/Book/saveVideo');
        Route::post('delvideo', 'admin/Book/delVideo');
        Route::post('dovideoshow', 'admin/Book/doVideoShow');
        Route::post('cancelvideoshow', 'admin/Book/cancelVideoShow');
        Route::rule('import', 'admin/Book/import')->middleware(AdminNav::class, 'book-bundle-import');
        Route::get('qrcode', 'admin/Book/qrcode');
    });
    Route::group('contact', function(){
        Route::get('list', 'admin/Contact/list')->middleware(AdminNav::class, 'contact-list');
        Route::get('save', 'admin/Contact/save')->middleware(AdminNav::class, 'contact-save');
        Route::post('save', 'admin/Contact/save');
        Route::post('del', 'admin/Contact/del');
        Route::post('editorder', 'admin/Contact/editOrder');
    });
    Route::group('bookcate', function(){
        Route::get('list', 'admin/BookCate/list')->middleware(AdminNav::class, 'bookcate-list');
        Route::get('save', 'admin/BookCate/save')->middleware(AdminNav::class, 'bookcate-save');
        Route::post('save', 'admin/BookCate/save');
        Route::post('del', 'admin/BookCate/del');
        Route::post('editorder', 'admin/BookCate/editOrder');
        Route::get('listselect', 'admin/BookCate/listSelect');
    });
    Route::group('draft', function(){
        Route::get('list', 'admin/Draft/list')->middleware(AdminNav::class, 'draft-list');
        Route::get('save', 'admin/Draft/save')->middleware(AdminNav::class, 'draft-save');
        Route::post('save', 'admin/Draft/save');
        Route::post('del', 'admin/Draft/del');
        Route::post('doshow', 'admin/Draft/doShow');
        Route::post('cancelshow', 'admin/Draft/cancelShow');
        Route::post('dorecomm', 'admin/Draft/doRecomm');
        Route::post('cancelrecomm', 'admin/Draft/cancelRecomm');
    });
    Route::group('banner', function(){
        Route::get('list', 'admin/Banner/list')->middleware(AdminNav::class, 'banner-list');
        Route::get('save', 'admin/Banner/save')->middleware(AdminNav::class, 'banner-save');
        Route::post('save', 'admin/Banner/save');
        Route::post('del', 'admin/Banner/del');
        Route::post('doshow', 'admin/Banner/doShow');
        Route::post('cancelshow', 'admin/Banner/cancelShow');
        Route::post('editorder', 'admin/Banner/editOrder');
    });
})->middleware(AdminAuth::class);

Route::rule('admin/useradmin/login', 'admin/UserAdmin/login');
Route::rule('admin/useradmin/tncode', 'admin/UserAdmin/tncode');
Route::rule('admin/useradmin/checktncode', 'admin/UserAdmin/checkTncode');

Route::group('wx_h5', function(){
    Route::group('index', function(){
        Route::get('index', 'wx_h5/Index/index');
    });
    Route::group('draft', function(){
        Route::get('recommend', 'wx_h5/Draft/recommend');
        Route::get('get', 'wx_h5/Draft/get');
        Route::get('index', 'wx_h5/Draft/index');
        Route::post('list', 'wx_h5/Draft/list');
    });
    Route::group('user', function(){
        Route::get('mybooks', 'wx_h5/User/myBooks');
        Route::get('index', 'wx_h5/User/index');
        Route::rule('info', 'wx_h5/User/info');
        Route::get('shelf', 'wx_h5/User/shelf');
        Route::get('draftletter', 'wx_h5/User/draftLetter');
        Route::get('contact', 'wx_h5/User/contact');
        Route::get('addr', 'wx_h5/User/addr');
        Route::rule('addrsave', 'wx_h5/User/addrSave');
        Route::post('addrdel', 'wx_h5/User/addrDel');
        Route::post('addrsetdefault', 'wx_h5/User/addrSetDefault');
        Route::post('putinshelf', 'wx_h5/User/putInShelf');
        Route::get('addrselect', 'wx_h5/User/addrSelect');
        Route::rule('addrselectsave', 'wx_h5/User/addrSelectSave');
        Route::post('docollectdraft', 'wx_h5/User/doCollectDraft');
        Route::post('cancelcollectdraft', 'wx_h5/User/cancelCollectDraft');
        Route::get('mydraft', 'wx_h5/User/myDraftView');
        Route::post('mydraft', 'wx_h5/User/myDraft');
    });
    Route::group('location', function(){
        Route::post('provinces', 'wx_h5/Location/provinces');
        Route::post('citys', 'wx_h5/Location/citys');
        Route::post('countys', 'wx_h5/Location/countys');
    });
    Route::group('book', function(){
        Route::get('index', 'wx_h5/Book/index');
        Route::get('search', 'wx_h5/Book/search');
        Route::get('list', 'wx_h5/Book/list');
        Route::get('get', 'wx_h5/Book/get');
        Route::get('recommend', 'wx_h5/Book/recommend');
        Route::get('videos', 'wx_h5/Book/videos');
        Route::get('test', 'wx_h5/Book/test');
    });
    Route::group('media', function(){
        Route::post('videoplayauth', 'wx_h5/Media/videoPlayAuth');
    });
})->middleware(Wxh5Auth::class);

return [

];
