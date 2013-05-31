<?php namespace Stwt\Mothership;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class MothershipUserModel extends MothershipModel implements UserInterface, RemindableInterface
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'last_login');

    /**
     * Returns the string description of the user
     *
     * @return string
     */
    public function __toString()
    {
        return $this->email;
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
