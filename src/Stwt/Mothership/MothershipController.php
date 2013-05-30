<?php namespace Stwt\Mothership;

use Auth;
use Controller;
use Config;
use Input;
use Redirect;
use View;
use URI;

class MothershipController extends Controller
{
    
    protected $breadcrumbs;

    public function __construct ()
    {
        $this->breadcrumbs  = ['/'  => 'Home'];
    }

    /*
     * Sets up common data required for the layout views
     *
     * @return array 
     */
    protected function getTemplateData()
    {
        $data = [];

        $data['breadcrumbs'] = $this->breadcrumbs;
        $data['navigation']  = Config::get('mothership::primaryNavigation');
        
        if (Auth::check()) {
            $data['user'] = Auth::user();
        }
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

    public function postLogin()
    {
        $credentials = ['email' => Input::get('email'), 'password' => Input::get('password')];

        if (Auth::attempt($credentials)) {
            Messages::add('success', 'You are now logged in');
            return Redirect::to('admin');
        }
        Messages::add('error', 'Login incorrect, please try again');
        return Redirect::to('admin/login');
    }

    public function getLogout()
    {
        Auth::logout();
        Messages::add('success', 'You have been logged out');
        return Redirect::to('admin/login');
    }
}
