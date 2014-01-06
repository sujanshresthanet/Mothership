<?php namespace Stwt\Mothership;

use Controller;

class BaseController extends Controller
{
    protected $breadcrumbs;

    public function __construct ()
    {
        $this->breadcrumbs  = ['/'  => 'Home'];
    }

    /**
     * Set's any default config attributes that have not
     * already been defined
     * 
     * @param Array $config
     * @param Array $defaults
     *
     * @return  Array
     */
    public function setDefaults(Array &$config, Array $defaults)
    {
        foreach ($defaults as $k => $v) {
            $config = Arr::s($config, $k, $v);
        }

        return $config;
    }
}
