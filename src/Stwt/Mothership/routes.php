<?php

/*
|--------------------------------------------------------------------------
| Mothership Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Config::get('mothership.controllers');

Route::get('admin/login',     'AdminHomeController@getLogin');
Route::post('admin/login',    'AdminHomeController@postLogin');

Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{
    Route::get('/',         'AdminHomeController@getIndex');
    Route::get('home',      'AdminHomeController@getIndex');
    Route::get('logout',    'AdminHomeController@getLogout');

    $controllers = Config::get('mothership.controllers');

    foreach ($controllers as $path => $class)
    {
        Route::resource($path, $class);
        Route::get($path.'/{id}/delete',  $class.'@delete');
        Route::get($path.'/{id}/meta',  $class.'@meta');
    }
});