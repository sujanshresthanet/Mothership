<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Controllers
	|--------------------------------------------------------------------------
	|
<<<<<<< HEAD
	| A URI to Resource Controller class map. Define which controller classes 
	| are used for each primary uri segment. The Hhome controllers is specified 
	| separately.
	| The following would use AdminUserController for any request to admin/users:
=======
	| A URI to Controller class map. Define which controller classes are used
	| for each primary uri segment. The following would use AdminUserController
	| for any request to admin/users:
>>>>>>> 35745a198b80f1fe71b390d87b330c284411b5c3
	|
	| 'users' => 'AdminUserController'
	|
	*/
    'controllers' => [
<<<<<<< HEAD
        'projects'  => 'AdminProjectController',
        'users'     => 'Stwt\BeHeart\AdminUserController',
        'images'    => 'AdminImageController',
        'tiles'     => 'AdminTileController',
=======
>>>>>>> 35745a198b80f1fe71b390d87b330c284411b5c3
    ],

	/*
	|--------------------------------------------------------------------------
<<<<<<< HEAD
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
=======
>>>>>>> 35745a198b80f1fe71b390d87b330c284411b5c3
	| Primary Navigation
	|--------------------------------------------------------------------------
	|
	| Set up the Primary navigation tree. The key defines the uri slug, the 
	| value is the label text.
	|
	*/
    'primaryNavigation' => [
        'home'      => 'Home',
<<<<<<< HEAD
        'projects'  => 'Projects',
        'users'     => 'Users',
        'tiles'     => 'Tiles',
        'images'    => 'Images',
=======
>>>>>>> 35745a198b80f1fe71b390d87b330c284411b5c3
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
