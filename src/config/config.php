<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Controllers
	|--------------------------------------------------------------------------
	|
	| A URI to Resource Controller class map. Define which controller classes 
	| are used for each primary uri segment. The Hhome controllers is specified 
	| separately.
	| The following would use AdminUserController for any request to admin/users:
	|
	| 'users' => 'AdminUserController'
	|
	*/
    'controllers' => [
        'projects'  => 'AdminProjectController',
        'users'     => 'Stwt\BeHeart\AdminUserController',
        'images'    => 'AdminImageController',
        'tiles'     => 'AdminTileController',
    ],

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| Set up the Primary navigation tree. The key defines the uri slug, the 
	| value is the label text.
	|
	*/
    'homeController' => 'AdminHomeController',

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
        'projects'  => 'Projects',
        'users'     => 'Users',
        'tiles'     => 'Tiles',
        'images'    => 'Images',
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
