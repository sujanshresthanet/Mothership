<?php namespace Stwt\Mothership;

use Illuminate\Support\ServiceProvider as ServiceProvider;
use Illuminate\Foundation\AliasLoader as AliasLoader;

class MothershipServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('stwt/mothership');

        include __DIR__.'/routes.php';
        include __DIR__.'/filters.php';
        include __DIR__.'/helpers.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        AliasLoader::getInstance()->alias('GoodForm', 'Stwt\GoodForm\GoodForm');
        AliasLoader::getInstance()->alias('Sluggable', 'Cviebrock\EloquentSluggable\Facades\Sluggable');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'Stwt\GoodForm\GoodFormServiceProvider',
            'Stwt\ImgYard\ImgYardServiceProvider',
            'Cviebrock\EloquentSluggable\SluggableServiceProvider',
        ];
    }
}
