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
$controllers = Config::get('mothership::controllers');

if ($controllers) {

    $homeController = Config::get('mothership::homeController');

    Route::get('admin/login', "$homeController@getLogin");
    Route::post('admin/login', "$homeController@postLogin");

    Route::group(
        array('prefix' => 'admin', 'before' => 'mothership'),
        function () use ($controllers, $homeController) {
            Route::get('/', "$homeController@getIndex");
            Route::get('home', "$homeController@getIndex");

            Route::get('profile', "$homeController@getProfile");
            Route::put('profile', "$homeController@putProfile");
            Route::get('password', "$homeController@getPassword");
            Route::put('password', "$homeController@putPassword");

            Route::get('logout', "$homeController@getLogout");
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
                 */
                Route::get(
                    $path.'/{id}/{method}',
                    function ($id, $method) use ($class) {
                        $controller = new $class ();
                        if (method_exists($controller, $method)) {
                            return $controller->{$method}($id);
                        } else {
                            throw new HTTPNotFoundException("Controller class does not have the method $method");
                            exit();
                        }
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
                // custom update routes
                Route::put(
                    $path.'/{id}/{method}',
                    function ($id, $method) use ($class) {
                        $controller = new $class ();
                        $method = 'update'.ucfirst($method);
                        error_log('call '.$method);
                        if (method_exists($controller, $method)) {
                            return $controller->{$method}($id);
                        } else {
                            throw new HTTPNotFoundException("Controller class does not have the method $method");
                            exit();
                        }
                    }
                );
                // DELETE REQUESTS
                // ---------------
                // destroy
                Route::delete($path.'/{id}', $class.'@destroy');
                // mass delete
                Route::delete($path, $class.'@destroyCollection');
            }
        }
    );
}
