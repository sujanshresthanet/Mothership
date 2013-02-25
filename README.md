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

Once Composer has installed or updated your packages you need to register the Mothership with Laravel. Open up app/config/app.php and add the following to the providers key.

    'Stwt\StwtServiceProvider',

Next you need to alias Mothership's facade. Find the aliases key which should be below the providers key.

    'Mothership'      => 'Stwt\Mothership\Mothership',

Finally, run __composer dump-autoload__ to updated your autoload class map