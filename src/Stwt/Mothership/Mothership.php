<?php namespace Stwt\Mothership;

use App;
use Config;
use URL;

class Mothership
{
    /**
     * Returns the full controler class for a path
     * 
     * @param string $path - The uri segment used in the admin
     * 
     * @return class
     */
    public static function controllerFromPath($path)
    {
        $controllers = Config::get('mothership::controllers');
        if (isset($controllers[$path])) {
            return $controllers[$path];
        }
        App::abort('404', 'Can`t find full classname for '.$path);
    }

    /**
     * Returns the path url for a full controler class
     * 
     * @param Class $class - The controller Class
     * 
     * @return string
     */
    public static function pathFromController($class)
    {
        $controllers = Config::get('mothership::controllers');
        //if (array_search($class, $controllers)) {
            return array_search($class, $controllers);
        //}
        //App::abort('404', 'Can`t find path for class '.$class);
    }

    /**
     * Returns the model class for a path
     * 
     * @param string $path - The uri segment used in the admin 
     * 
     * @return class
     */
    public static function modelFromPath($path)
    {
        $class = self::controllerFromPath($path);
        
        return with(new $class)->model;
    }

    /**
     * Returns an instanciated instance of a controllers model
     * 
     * @param string $path - The uri segment used in the admin
     * @param int $id      - The instance id [optional]
     * 
     * @return object
     */
    public static function resourceFromPath($path, $id = null)
    {
        $class = self::modelFromPath($path);
        if ($id) {
            return $class::find($id);
        } else {
            return new $class;
        }
    }
}
