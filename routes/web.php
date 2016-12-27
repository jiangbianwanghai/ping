<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//默认监控列表
Route::get('/', 'Home@board');

//新增监控任务
Route::get('/add', function () {
    return view('add');
});

//编辑个人资料
Route::get('/profile', 'Home@profile');

//快照预览
Route::get('/cache/{id}', function ($id) {
    $flag = $time = 0;
    if (Storage::exists($id.'.cache')) {
        $flag = 1;
        $time = Storage::lastModified($id.'.cache');
    }
    return view('cache', ['id' => $id, 'html' => 1, 'time' => $time, 'flag' => $flag]);
});

//编辑监控任务表单
Route::get('/job/edit/{id}', 'Home@editJob');

//监控任务详情
Route::get('/detail/{id}', 'Home@detail');

//输出快照html
Route::get('/html/{id}', 'Home@html');

//后端处理-新增监控任务请求
Route::post('/post', 'Home@post');

//后端处理-个人资料更新
Route::post('/profile/update', 'Home@profileUpdate');

//后端处理-修改监控任务请求
Route::post('/update', 'Home@updateJob');

//后端处理-输出默认监控列表
Route::get('/board/api/{offset}', 'Home@boardApi');

//后端处理-开启/暂停监控任务
Route::get('/working/{id}/{act}', 'Home@working');

//后端处理-输出详情页响应时间图表用json数据
Route::get('/detail/xysj/{id}', 'Home@xysj');

//后端处理-输出详情页可用率图表用json数据
Route::get('/detail/kyl/{id}', 'Home@kyl');

//后端处理-输出详情页的表格数据
Route::get('/detail/table/{id}/{offset}', 'Home@table');

//后端处理-输出详情页状态码图表用json数据
Route::get('/detail/ztm/{id}', 'Home@ztm');

//后端处理-输出详情页监控任务摘要
Route::get('/detail/sumary/{id}', 'Home@sumary');

//后端处理-删除监控任务
Route::get('/api/del_job/{id}', 'Home@delJob');

//后端处理-抓取监控任务的快照
Route::get('/snapshot/{id}', 'Home@snapshot');

//发送用户验证授权链接
Route::group(['prefix' => 'auth'], function(){
    Route::get('email', function () {
        return view('email');
    });
    Route::post('postoken', 'Home@postoken');
    Route::get('signin/{token}', 'Home@signin');
});
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

//关注
Route::group(['prefix' => 'star'], function(){
    Route::get('add/{id}', 'Home@starAdd');
    Route::get('remove/{id}', 'Home@starRemove');
});
