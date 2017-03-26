<?php
Route::get('/', [
    'middleware' => 'checkLogin',
    'uses' => 'HomeController@index'
]);

Route::get('/login', 'LoginController@index');
