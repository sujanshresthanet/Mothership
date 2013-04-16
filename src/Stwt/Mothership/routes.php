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

if (Config::get('mothership.controllers')) {

    Route::get('admin/login', 'AdminHomeController@getLogin');
    Route::post('admin/login', 'AdminHomeController@postLogin');

    Route::group(
        array('prefix' => 'admin', 'before' => 'mothership'),
        function () {
            Route::get('/', 'AdminHomeController@getIndex');
            Route::get('home', 'AdminHomeController@getIndex');
            Route::get('logout', 'AdminHomeController@getLogout');

            $controllers = Config::get('mothership.controllers');
            
            foreach ($controllers as $path => $class) {
                //Route::resource($path, $class);
                
                // GET REQUESTS
                // ------------
                // index
                Route::get($path, $class.'@index');
                // create
                Route::get($path.'/create', $class.'@create');
                // view
                Route::get(
                    $path.'/{id}',
                    function ($id) use ($class) {
                         $controller = new $class ();
                        return $controller->show($id);
                    }
                );
                /*
                 * /{id}/{edit}
                 * Route to an edit view/form on a specific model.
                 * If the {method} method does not exist in the controller
                 * the request will be handled by edit($id);
                 */
                Route::get(
                    $path.'/{id}/{method}',
                    function ($id, $method) use ($class) {
                        $controller = new $class ();
                        if (method_exists($controller, $method)) {
                            return $controller->{$method}($id);
                        }
                        return $controller->edit($id);
                    }
                );
                // related index
                Route::get(
                    $path.'/index/{model}/{id}',
                    function ($model, $id) use ($class) {
                        $controller = new $class ();
                        return $controller->index($model, $id);
                    }
                );
                // create related
                Route::get(
                    $path.'/create/{model}/{id}',
                    function ($model, $id) use ($class) {
                        $controller = new $class ();
                        return $controller->create($model, $id);
                    }
                );
                // POST REQUESTS
                // -------------
                // store
                Route::post($path, $class.'@store');
                Route::post(
                    $path.'/{model}/{id}',
                    function ($model, $id) use ($class) {
                        error_log('create related');
                        $controller = new $class ();
                        return $controller->store($model, $id);
                    }
                );
                // PUT REQUESTS
                // ------------
                // update
                Route::put($path.'/{id}', $class.'@update');
                // DELETE REQUESTS
                // ---------------
                // destroy
                Route::delete($path.'/{id}', $class.'@destroy');
            }
        }
    );
}