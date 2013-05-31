<?php namespace Stwt\Mothership;

use Auth;
use Controller;
use Config;
use Input;
use Redirect;
use Log;
use View;
use URI;
use URL;
use Stwt\GoodForm\GoodForm as GoodForm;
use Validator;

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
        $data = [];

        $data['title'] = 'Hi There!';
        $data['content'] = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        return View::make('mothership::home.index')
            ->with($this->getTemplateData())
            ->with($data);
    }

    public function getLogin()
    {
        return View::make('mothership::home.login')->with($this->getTemplateData());
    }

    /**
     * Render a form to update user profiles
     * 
     * @return View
     */
    public function getProfile()
    {
        $data = [];

        $form = new GoodForm;
        $user = \User::find(Auth::user()->id);

        // add field to store request type
        $methodField = ['type' => 'hidden', 'name' => '_method', 'value' => 'PUT'];
        $form->add($methodField);

        $fields = $user->getFields();
        foreach ($fields as $name => $field) {
            $field->value = $user->{$name};
            $form->add($field);
        }

        // Add form actions
        $form->addAction(
            [
                'class' => 'btn btn-primary',
                'form'  => 'button',
                'name'  => '_save',
                'type'  => 'submit',
                'value' => 'Save',
            ]
        );
        $form->addAction(
            [
                'class' => 'btn',
                'form'  => 'button',
                'name'  => '_cancel',
                'type'  => 'reset',
                'value' => 'Cancel',
            ]
        );

        $formAttr = [
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data['title'] = 'Your Profile';
        $data['content'] = $form->generate();

        return View::make('mothership::home.index')
            ->with($data)
            ->with($this->getTemplateData());
    }

    /**
     * Update the logged in users profile
     *
     * @return Redirect
     */
    public function putProfile()
    {
        $user = \User::find(Auth::user()->id);

        $data   = Input::all();
        $fields = array_keys($data);
        $rules  = $user->getRules($fields);
        
        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            $message = 'There was an error updating your profile. Please correct errors in the Form.';
            Messages::add('error', $message);
            return Redirect::to(URL::to('admin/profile'))
                ->withInput()
                ->withErrors($validation);
        } else {
            foreach ($fields as $field) {
                if ($user->hasProperty($field) AND Input::get($field)) {
                    $user->$field = Input::get($field);
                }
            }
            if ($user->save()) {
                $message = 'Profile updated successfully!';
                Messages::add('success', $message);
            }
            return Redirect::to(URL::to('admin/profile'));
        }
        
    }

    public function getPassword()
    {
        return 'Password';
    }

    public function postLogin()
    {
        $credentials = ['email' => Input::get('email'), 'password' => Input::get('password')];

        if (Auth::attempt($credentials)) {
            Messages::add('success', 'You are now logged in');
            
            $user = Auth::user();
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

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
