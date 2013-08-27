<?php namespace Stwt\Mothership;

use Hash;
use Illuminate\Auth\UserInterface as UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface as RemindableInterface;

class UserModel extends BaseModel implements UserInterface, RemindableInterface
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $properties = [
        'password' => [
            'type' => 'password',
        ],
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'last_login');

    protected $guarded = [
        'id',
        'password',
        'last_login',
        'permissions',
        'activation_code',
        'activated_at',
        'persist_code',
        'reset_password_code',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    /**
     * Returns the string description of the user
     *
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }

    public function beforeSave()
    {
        // if there's a new password, hash it
        if ($this->isDirty('password')) {
            $this->password = Hash::make($this->password);
        }

        return true;
        //or don't return nothing, since only a boolean false will halt the operation
    }

    /**
     * Returns the users full name
     *
     * @return string
     */
    public function displayName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function lastLogin()
    {
        if (!$this->last_login) {
            return 'never…';
        }
        $date = new \ExpressiveDate($this->last_login);
        return $date->getRelativeDate();
    }

    /**
     * Returns true if the user can update their own profile
     * Override this with false if you are using a third party auth system like ActiveDirectory
     * 
     * @return boolean
     */
    public function canUpdateProfile()
    {
        return true;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }
}
