# Mothership

A Laravel based admin app framework.

## Features

- Quickly setup a CRUD admin app
- Automatically generates forms suited to your models 
- Twitter Bootstrap stylesheets

## Requirements

- Laravel 4
- PHP 5.4.*

## Installation

Add the following to your root composer.json

    "stwt/mothership": "*"

Update your packages with __composer update__ or install with __composer install__.

### Update app.php

Once Composer has installed or updated your packages you need to register the Mothership with Laravel. Open up _app/config/app.php_ and add the following to the providers key.

    'Stwt\Mothership\MothershipServiceProvider',

Next you need to alias Mothership's facade. Find the aliases key which should be below the providers key.

    'Mothership'      => 'Stwt\Mothership\Mothership',

### Assets

Mothership comes with package assets (css & js files). Publish these to your _public_ directory.

	php artisan asset:publish stwt/mothership
	
### Config

Mothership also comes with a config file. Publish this to your _app/config_ directory.

	php artisan config:publish stwt/mothership

Finally, run __composer dump-autoload__ to updated your autoload class map

## Creating your first Mothership

### Models

Any models to be used in the mothship should extend the **MothershipModel** class.

    use Stwt\Mothership\MothershipModel as MothershipModel;

    class Thing extends MothershipModel {
        //...
    }

### Controllers

Resource controllers should extends the **MothershipResourceController** class. 

    use Stwt\Mothership\MothershipResourceController as MothershipResourceController;

    class ThingController extends MothershipResourceController {
        //...
    }

### Routes

Add the new resource controller to your __app/routes.php__ file.

    Route::resource('admin/things', 'ThingController');

**Done.**
