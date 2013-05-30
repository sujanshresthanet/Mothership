<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Controllers
	|--------------------------------------------------------------------------
	|
	| A URI to Controller class map. Define which controller classes are used
	| for each primary uri segment. The following would use AdminUserController
	| for any request to admin/users:
	|
	| 'users' => 'AdminUserController'
	|
	*/
    'controllers' => [
    ],

	/*
	|--------------------------------------------------------------------------
	| Primary Navigation
	|--------------------------------------------------------------------------
	|
	| Set up the Primary navigation tree. The key defines the uri slug, the 
	| value is the label text.
	|
	*/
    'primaryNavigation' => [
        'home'      => 'Home',
    ],

	/*
	|--------------------------------------------------------------------------
	| Cache
	|--------------------------------------------------------------------------
	|
	| Controlls the caching used by Mothership. If cache is set to true, we
	| cache things like Model column definition to speed up the application.
	|
	*/
    'cache' => false,
];
