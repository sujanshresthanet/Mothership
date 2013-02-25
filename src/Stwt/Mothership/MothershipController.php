<?php namespace Stwt\Mothership;

use Auth;
use Controller;
use Config;
use Input;
use View;
use URI;

class MothershipController extends Controller {
    
    protected $breadcrumbs;

    function __construct () {
        $this->breadcrumbs  = ['/'  => 'Home'];
    }

    protected function getTemplateData()
    {
        $data = [];

        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['navigation']  = Config::get('mothership.primaryNavigation');

        return $data;
    }

    public function getIndex() 
    {
        return View::make('mothership::home.index')->with($this->getTemplateData());
    }

    public function getLogin()
    {
        return View::make('mothership::home.login')->with($this->getTemplateData());
    }

    public function getLogout()
    {
        return 'Logout';
    }
}