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


$homeController = Config::get('mothership::homeController');

Route::get('admin/login', "$homeController@getLogin");
Route::post('admin/login', "$homeController@postLogin");
Route::get('admin/logout', "$homeController@getLogout");

$controllers = Config::get('mothership::controllers');

if ($controllers) {

    Route::group(
        [
            'prefix' => 'admin',
            'before' => 'mothership',
        ],
        function () use ($controllers, $homeController) {

            Route::get('/', "$homeController@getIndex");
            Route::get('home', "$homeController@getIndex");



            View::composer('mothership::theme.layouts.base', 'Stwt\Mothership\Composer\Base');
            View::composer('mothership::theme.layouts.sidebar', 'Stwt\Mothership\Composer\Sidebar');
            View::composer('mothership::theme.layouts.single', 'Stwt\Mothership\Composer\Single');
            View::composer('mothership::theme.layouts.table', 'Stwt\Mothership\Composer\Resource\Table');

            Route::get(
                '/bug/view/base',
                function () {
                    return View::make('mothership::theme.layouts.base');
                }
            );
            Route::get(
                '/bug/view/single',
                function () {
                    return View::make('mothership::theme.layouts.single');
                }
            );
            Route::get(
                '/bug/view/sidebar',
                function () {
                    return View::make('mothership::theme.layouts.sidebar');
                }
            );
            Route::get(
                '/bug/view/table',
                function () {
                    return View::make('mothership::theme.layouts.table');
                }
            );

            Route::get('profile', "$homeController@getProfile");
            Route::put('profile', "$homeController@putProfile");
            Route::get('password', "$homeController@getPassword");
            Route::put('password', "$homeController@putPassword");

            Route::post(
                'upload/file',
                function () {
                    try {
                        if (Input::hasFile('file')) {
                            $file     = Input::file('file');
                            $ext      = $file->getClientOriginalExtension();
                            $filename = $file->getClientOriginalName();
                            $name     = Input::get('filename', $filename);
                            $path     = public_path()."/uploads/files/";

                            Input::file('file')->move($path, $filename);

                            $data = array(
                                'filelink' => URL::asset('uploads/files/'.$filename),
                                'filename' => $name,
                            );
                            return Response::json($data, 200);
                        } else {
                            return Response::json(['error' => 'Error uploading file'], 400);
                        }
                    } catch (Exception $e) {
                        return Response::json(['error' => 'Error uploading file'], 500);
                    }
                }
            );

            foreach ($controllers as $path => $class) {
                // index
                Route::get(
                    $path,
                    function () use ($class, $path) {
                        $config = [
                            'controller' => $class,
                            'action'     => 'index',
                            'type'       => 'collection',
                            'path'       => $path,
                        ];
                        return with(new $class)->index($config);
                    }
                );

                // create / index
                Route::get(
                    $path.'/{method}',
                    function ($method) use ($class, $path) {
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'type'       => 'collection',
                            'path'       => $path,
                        ];
                        return with(new $class)->$method($config);
                    }
                )->where('method', '[A-Za-z]+');
                
                // view
                Route::get(
                    $path.'/{id}',
                    function ($id) use ($class, $path) {
                        $config = [
                            'controller' => $class,
                            'action'     => 'show',
                            'id'         => $id,
                            'type'       => 'resource',
                            'path'       => $path,
                        ];
                        return with(new $class)->show($id);
                    }
                )->where('id', '[0-9]+');
                
                // edit/delete
                Route::get(
                    $path.'/{idMethod}',
                    function ($idMethod) use ($class, $path) {
                        list($id, $method) = explode(':', $idMethod);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'resource',
                            'path'       => $path,
                        ];
                        if (!method_exists($class, $method)) {
                            $method = 'edit';
                        }
                        return with(new $class)->{$method}($id, $config);
                    }
                );

                // store
                Route::post(
                    $path.'/create',
                    function () use ($class) {
                        $config = [
                            'controller' => $class,
                            'action'     => 'create',
                            'type'       => 'store',
                        ];
                        return with(new $class)->store($config);
                    }
                );

                // update
                Route::put(
                    $path.'/{idMethod}',
                    function ($idMethod) use ($class) {
                        Log::error('PUT update '.$idMethod);
                        list($id, $method) = explode(':', $idMethod);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'update',
                        ];
                        $method = 'update'.ucfirst($method);
                        if (!method_exists($class, $method)) {
                            $method = 'update';
                        }
                        return with(new $class)->{$method}($id, $config);
                    }
                );


                // multi destroy
                Route::delete(
                    $path,
                    function () use ($class) {

                        $config = [
                            'controller' => $class,
                            'action'     => 'delete',
                            'type'       => 'destroy',
                        ];
                        return with(new $class)->multiDestroy($config);
                    }
                )->where('id', '[0-9]+');

                // destroy
                Route::delete(
                    $path.'/{id}:delete',
                    function ($id) use ($class) {

                        $config = [
                            'controller' => $class,
                            'action'     => 'delete',
                            'id'         => $id,
                            'type'       => 'destroy',
                        ];
                        return with(new $class)->destroy($id, $config);
                    }
                )->where('id', '[0-9]+');

                #########################################
                # Related Routes                        #
                #########################################

                // related index
                Route::get(
                    '{relatedPath}/{relatedId}/'.$path,
                    function ($relatedPath, $relatedId) use ($class) {
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => 'index',
                            'type'       => 'collection',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->index($config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+');

                // related create
                Route::get(
                    '{relatedPath}/{relatedId}/'.$path.'/{method}',
                    function ($relatedPath, $relatedId, $method) use ($class) {
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'type'       => (in_array($method, ['index', 'resource']) ? 'collection' : 'single'),
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->$method($config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+')
                ->where('method', '[A-Za-z]+');

                // related view
                Route::get(
                    '{relatedPath}/{relatedId}/'.$path.'/{id}',
                    function ($relatedPath, $relatedId, $id) use ($class) {
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'resource',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->show($id, $config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+')
                ->where('id', '[0-9]+');

                // related edit
                Route::get(
                    '{relatedPath}/{relatedId}/'.$path.'/{idMethod}',
                    function ($relatedPath, $relatedId, $idMethod) use ($class) {
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        list($id, $method) = explode(':', $idMethod);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'resource',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        if (!method_exists($class, $method)) {
                            $method = 'edit';
                        }
                        return with(new $class)->$method($id, $config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+');

                // store
                Route::post(
                    '{relatedPath}/{relatedId}/'.$path.'/{method}',
                    function ($relatedPath, $relatedId, $method) use ($class) {
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'type'       => 'store',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->store($config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+')
                ->where('method', '[A-Za-z]+');

                // update
                Route::put(
                    '{relatedPath}/{relatedId}/'.$path.'/{idMethod}',
                    function ($relatedPath, $relatedId, $idMethod) use ($class) {
                        list($id, $method) = explode(':', $idMethod);
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'update',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->update($id, $config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+');

                // destroy
                Route::delete(
                    '{relatedPath}/{relatedId}/'.$path.'/{idMethod}',
                    function ($relatedPath, $relatedId, $idMethod) use ($class) {
                        list($id, $method) = explode(':', $idMethod);
                        $relatedResource = Mothership::resourceFromPath($relatedPath, $relatedId);
                        $config = [
                            'controller' => $class,
                            'action'     => $method,
                            'id'         => $id,
                            'type'       => 'update',
                            'related' => [
                                'path'      => $relatedPath,
                                'id'        => $relatedId,
                                'resource'  => $relatedResource,
                                'uri'       => $relatedPath.'/'.$relatedId.'/',
                            ]
                        ];
                        return with(new $class)->destroy($id, $config);
                    }
                )
                ->where('relatedPath', '[A-Za-z]+')
                ->where('relatedId', '[0-9]+');
            }
        }
    );
}





return;


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
