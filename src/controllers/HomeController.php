<?php namespace Stwt\Mothership;

use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\Config as Config;
use Illuminate\Support\Facades\Hash as Hash;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Redirect as Redirect;
use User;
use Log;
use Sentry;

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
    public function getLogin($config = [])
    {
        // set default config variable for this view
        $this->setDefaults(
            $config,
            [
                'view'          => 'mothership::theme.home.login',
                'viewComposer'  => 'Stwt\Mothership\Composer\Single',
            ]
        );

        $data['title'] = 'Please Login';

        // get the view template and view composer to use
        $view         = Arr::e($config, 'view');
        $viewComposer = Arr::e($config, 'viewComposer');
        
        // Attach a composer to the view
        View::composer($view, $viewComposer);

        return View::make($view, $data);
    }

    /**
     * Attempt to login the user
     *
     * @return Redirect
     */
    public function postLogin()
    {
        try {
            // Set login credentials
            $credentials = [
                'username' => Input::get('username'),
                'password' => Input::get('password'),
            ];
            // get the remember me value
            $rememberMe = Input::has('remember_me');
            // Try to authenticate the user
            $user = Sentry::authenticate($credentials, $rememberMe);
            // Find the Administrator group
            $admin = Sentry::getGroupProvider()->findByName('Administrator');

            if ($user->inGroup($admin)) {
                // User is in Administrator group
                Messages::add('success', 'You are now logged in');
                return Redirect::to('admin');
            } else {
                // User is not in Administrator group - log out
                Sentry::logout();
                Messages::add('error', 'Access is restricted to Administrators.');
            }
        } catch (\Cartalyst\Sentry\Users\LoginRequiredException $e) {
            Messages::add('error', 'Email field is required.');
        } catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e) {
            Messages::add('error', 'Password field is required.');
        } catch (\Cartalyst\Sentry\Users\WrongPasswordException $e) {
            Messages::add('error', 'Wrong password, try again.');
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            Messages::add('error', 'User was not found.');
        } catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e) {
            Messages::add('error', 'User is not activated.');
        } catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
            Messages::add('error', 'User is suspended.');
        } catch (\Cartalyst\Sentry\Throttling\UserBannedException $e) {
            Messages::add('error', 'User is banned.');
        }
        return Redirect::to('admin/login');
    }

    /**
     * Log the user out of the admin
     *
     * @return Redirect
     */
    public function getLogout()
    {
        Sentry::logout();
        Messages::add('success', 'You have been logged out');
        return Redirect::to('admin/login');
    }

    /**
     * Render the admin homepage
     *
     * @return View
     */
    public function getIndex($config = [])
    {
        $data = $config;

        $data = Arr::s($data, 'title', 'Hi There!');

        $data = Arr::s(
            $data,
            'content',
            'Lorem ipsum dolor sit amet, consectetur 
        adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore 
        magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
        ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute 
        irure dolor in reprehenderit in voluptate velit esse cillum dolore eu 
        fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, 
        sunt in culpa qui officia deserunt mollit anim id est laborum.'
        );

        return View::makeTemplate('mothership::theme.home.index')
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

        $userClass = Config::get('auth.model');

        $userId = Auth::user()->id;
        $user   = $userClass::find($userId);

        $form = FormGenerator::resource($user)
            ->method('put')
            ->form()
            ->generate();

        $data['title'] = 'Your Profile';
        $data['content'] = $form;

        return View::makeTemplate('mothership::theme.home.index')
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
        $userClass = Config::get('auth.model');
        $userId = Auth::user()->id;
        $user   = $userClass::find($userId);

        return FormGenerator::resource($user)
            ->errorMessage('There was an error updating your profile. Please correct errors in the Form.')
            ->successMessage('Profile updated successfully!')
            ->save()
            ->redirect('admin/profile');
    }

    /**
     * Render a form to update user password
     *
     * @return View
     */
    public function getPassword()
    {
        $data = [];

        $userClass = Config::get('auth.model');
        $userId = Auth::user()->id;
        $user   = $userClass::find($userId);

        $fields = $this->getPasswordFields($user);

        $form = FormGenerator::resource($user)
            ->method('put')
            ->fields($fields)
            ->form()
            ->generate();

        $data['title'] = 'Change Password';
        $data['content'] = $form;

        return View::makeTemplate('mothership::theme.home.index')
            ->with($data)
            ->with($this->getTemplateData());
    }

    /**
     * Update the users password
     *
     * @return Redirect
     */
    public function putPassword()
    {
        $userClass = Config::get('auth.model');
        $userId = Auth::user()->id;
        $user   = $userClass::find($userId);

        $rules = $this->getPasswordRules($user);

        return FormGenerator::resource($user)
            ->rules($rules)
            ->beforeSave(
                function ($resource) {
                     $resource->password = Hash::make($resource->password);
                }
            )
            ->errorMessage('There was an error updating your password. Please correct errors in the Form.')
            ->successMessage('Password updated successfully!')
            ->save()
            ->redirect('admin/password');
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
    protected function getPasswordFields($user)
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
     * Returns the rules for the update password form. This is a password
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
    public function getPasswordRules($user)
    {
        $passwordRules = $user->getPropery('password')->validation;

        $confirmationRules = $passwordRules;
        $passwordRules[]   = 'confirmed';

        return [
            'password'              => $passwordRules,
            'password_confirmation' => $confirmationRules
        ];
    }
}
