<?php

Route::post('/test/test', 'TestController@test');
Route::get('/test/test', 'TestController@test');
Route::get('/', [
    'middleware' => 'checkLogin',
    'uses' => 'HomeController@index'
]);

Route::get('/login', 'LoginController@index');
Route::post('/login/check', 'LoginController@check');
Route::get('/logout', 'LoginController@logout');

Route::get('/main', [
    'middleware' => 'checkLogin',
    'uses' => 'MainController@main'
]);
Route::group([
    'prefix' => 'main',
    'middleware' => 'checkLogin'
], function () {
    Route::get('/', 'MainController@main');
    Route::post('/editmobile', 'MainController@editMobile');
    Route::match(['get', 'post'], '/editpassword', 'MainController@editPassword');
});

//系统
Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['checkLogin']
], function () {
    //用户
    Route::get('/user', 'UserController@user');
    Route::match(['get', 'post'], '/user/add', 'UserController@userAdd');
    Route::match(['get', 'post'], '/user/edit/{id}', 'UserController@userEdit');
    Route::get('/user/ajaxDBUser', 'UserController@ajaxDBUser');
    Route::match(['get', 'post'], '/user/pwdEdit', 'UserController@userPwdEdit');
    Route::match(['get', 'post'], '/user/pwdModify', 'UserController@userPwdModify');


    //角色
    Route::get('/role', 'RoleController@role');
    Route::post('/role/user/{id}', 'RoleController@user');
    Route::post('/role/add', 'RoleController@roleAdd');
    Route::post('/role/edit/{id}', 'RoleController@roleEdit');
    Route::get('/role/del/{id}', 'RoleController@roleDel');
    //角色分配权限
    Route::get('/roleaction/set/{id}', 'RoleActionController@set');
    Route::post('/roleaction/edit', 'RoleActionController@edit');
    //权限
    Route::get('/action', 'ActionController@action');
    Route::post('/action/tree', 'ActionController@getTree');
    Route::post('/action/add', 'ActionController@addInfo');
    Route::post('/action/get', 'ActionController@getInfo');
    Route::post('/action/del', 'ActionController@delInfo');
    Route::get('/action/updatecode', 'ActionController@updateCode');
    //日志
    Route::get('/log', 'LogController@log');
    Route::post('/log/info/{id}', 'LogController@logInfo');
    //白名单
    Route::match(['get', 'post'],'/whitelist', 'WhitelistController@whitelist');
    Route::match(['get', 'post'],'/whitelist/add', 'WhitelistController@whiteAdd');
    Route::post('/whitelist/addCheck', 'WhitelistController@addCheck');
    Route::post('/whitelist/del', 'WhitelistController@whiteDel');
});