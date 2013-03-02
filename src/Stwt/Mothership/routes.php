<?php
/*
|--------------------------------------------------------------------------
| Mothership Routes
|--------------------------------------------------------------------------
|
| Here is where we register all the resources you manage in the Mothership.
| We also register other routes used in the admin like the homepage and auth
| pages.
|
*/

Config::get('mothership.controllers');

Route::get('admin/login', 'AdminHomeController@getLogin');
Route::post('admin/login', 'AdminHomeController@postLogin');

Route::group(
    array('prefix' => 'admin', 'before' => 'auth'),
    function () {
        Route::get('/', 'AdminHomeController@getIndex');
        Route::get('home', 'AdminHomeController@getIndex');
        Route::get('logout', 'AdminHomeController@getLogout');

        $controllers = Config::get('mothership.controllers');
        
        foreach ($controllers as $path => $class) {
            Route::resource($path, $class);
            Route::get($path.'/{id}/delete', $class.'@delete');
            Route::get($path.'/{id}/meta', $class.'@meta');
            error_log($class);
            // related 
            Route::get(
                $path.'/{model}/{id}',
                function ($model, $id) use ($class) {
                    error_log($class);
                    $controller = new $class ();
                    return $controller->index($model, $id);
                }
            );
        }
    }
);
