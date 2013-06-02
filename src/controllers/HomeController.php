<?php namespace Stwt\Mothership;

use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\View as View;
use User;

/**
 * HomeController
 *
 * Base class for your admin Home controller. 
 * Contains actions for:
 * - Login              @getLogin/@postLogin
 * - Logout             @getLogout
 * - Homepage           @getIndex
 * - Update Profiles    @getProfile/@postProfile
 * - Change Password    @getPassword/@postPassword
 */
class HomeController extends BaseController
{
    /**
     * Render a form to login a user to the admin
     *
     * @return View
     */
    public function getLogin()
    {
        return View::make('mothership::home.login')->with($this->getTemplateData());
    }

    /**
     * Attempt to login the user
     *
     * @return Redirect
     */
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

    /**
     * Log the user out of the admin
     *
     * @return Redirect
     */
    public function getLogout()
    {
        Auth::logout();
        Messages::add('success', 'You have been logged out');
        return Redirect::to('admin/login');
    }

    /**
     * Render the admin homepage
     *
     * @return View
     */
    public function getIndex()
    {
        $data = [];

        $data['title'] = 'Hi There!';
        $data['content'] = 'Lorem ipsum dolor sit amet, consectetur 
        adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore 
        magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
        ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute 
        irure dolor in reprehenderit in voluptate velit esse cillum dolore eu 
        fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, 
        sunt in culpa qui officia deserunt mollit anim id est laborum.';

        return View::make('mothership::home.index')
            ->with($this->getTemplateData())
            ->with($data);
    }

    /**
     * Render a form to update user profiles
     * 
     * @return View
     */
    public function getProfile()
    {
        $data = [];

        $userId = Auth::user()->id;
        $user   = User::find($userId);

        $form = FormGenerator::resource($user)
            ->method('put')
            ->form();

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
        $userId = Auth::user()->id;
        $user   = User::find($userId);

        return FormGenerator::resource($user)
            ->errorMessage('There was an error updating your profile. Please correct errors in the Form.')
            ->saveMessage('Profile updated successfully!')
            ->save()
            ->redirect('admin/profile');

        /*
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
                if ($user->hasProperty($field) and Input::get($field)) {
                    $user->$field = Input::get($field);
                }
            }
            if ($user->save()) {
                $message = 'Profile updated successfully!';
                Messages::add('success', $message);
            }
            return Redirect::to(URL::to('admin/profile'));
        }
        */
    }

    /**
     * Render a form to update user password
     *
     * @return View
     */
    public function getPassword()
    {
        $data = [];

        $userId = Auth::user()->id;
        $user   = User::find($userId);

        $fields = $this->getUpdatePasswordFields($user);

        $form = FormGenerator::resource($user)
            ->method('put')
            ->fields($fields)
            ->form();

        $formAttr = [
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];

        $form->attr($formAttr);

        $data['title'] = 'Change Password';
        $data['content'] = $form->generate();

        return View::make('mothership::home.index')
            ->with($data)
            ->with($this->getTemplateData());
    }

    /**
     * Returns the fields for the update password form. This is a password
     * and a confirm field. The confirm field is a virtual field, so we 
     * construct an array spec for it.
     *
     * Get current rules assigned to the password property the confirm field
     * will also need to match these rules.
     *
     * Add 'confirmed' rule to password - so it must match the virtual field
     * 
     * @param object $user
     *
     * @return array
     */
    protected function getUpdatePasswordFields($user)
    {
        $rules = $user->getPropery('password')->validation;
        $user->addRule('password', 'confirmed');
        return [
            'password',
            'password_confirmation' => [
                'label'      => 'Confirm password',
                'name'       => 'password_confirmation',
                'type'       => 'password',
                'validation' => $rules,
            ],
        ];
    }

    /**
     * Update the users password
     *
     * @return Redirect
     */
    public function putPassword()
    {
        $user = \User::find(Auth::user()->id);

        $data   = Input::all();
        $fields = array_keys($data);
        
        $passwordRules = $user->getPropery('password')->validation;

        $confirmationRules = $passwordRules;
        $passwordRules[]   = 'confirmed';

        $rules = [
            'password'              => $passwordRules,
            'password_confirmation' => $confirmationRules
        ];
        
        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            $message = 'There was an error updating your password. Please correct errors in the Form.';
            Messages::add('error', $message);
            return Redirect::to(URL::to('admin/password'))
                ->withInput()
                ->withErrors($validation);
        } else {
            $user->password = Hash::make($data['password']);

            if ($user->save()) {
                $message = 'Password updated successfully!';
                Messages::add('success', $message);
            }
            return Redirect::to(URL::to('admin/password'));
        }
    }
}
