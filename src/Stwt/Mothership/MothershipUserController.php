<?php namespace Stwt\Mothership;

use Hash;
use Input;
use Request;
use URL;

class MothershipUserController extends MothershipResourceController
{
    /**
     * Class name of the Resource model this controller uses
     */
    public static $model = 'User';

    /**
     * Actions in this controller
     * The User controller comes complete with an extra password action
     */
    public $actions = [
        'update' => [
            'view' => [
                'label' => 'View',
                'uri' => '{controller}/{id}',
            ],
            'edit' => [
                'label' => 'Edit',
                'uri' => '{controller}/{id}/edit',
            ],
            'password' => [
                'label' => 'Password',
                'uri' => '{controller}/{id}/password',
            ],
            'history' => [
                'label' => 'History',
                'uri' => '{controller}/{id}/history',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri' => '{controller}/{id}/delete',
            ],
        ],
        'related' => [
        ],
        'create' => [
            'create' => [
                'label' => 'Add User',
                'uri' => '{controller}/create',
            ],
        ],
    ];

    /**
     * A custom update action that provides the user with a form
     * to change their password.
     *
     * We add the password field and a pusudo password conformation field 
     * to the form and handle the PUT request in the updatePassword method.
     *
     * @param int   $id     the resource id
     * @param array $config override defaults in the password view
     *
     * @return View
     */
    public function password($id, $config = [])
    {
        if (!Arr::e($config, 'fields')) {
            $config['fields'] = $this->getPasswordFormFields();
        }
        if (!Arr::e($config, 'action')) {
            $config['action'] = URL::to('admin/'.$this->controller.'/'.$id.'/'.$this->method);
        }
        if (!Arr::e($config, 'breadcrumb')) {
            $config['breadcrumb'] = 'Password';
        }

        return $this->edit($id, $config);
    }

    /**
     * Updated the users password
     *
     * @param int   $id the resource id
     * @param array $id override default handling of form
     *
     * @return   void    (redirect) 
     */
    public function updatePassword($id, $config = [])
    {
        if (!Arr::e($config, 'rules')) {
            $config['rules'] = $this->getPasswordFormRules();
        }
        if (!Arr::e($config, 'beforeSave')) {
            $config['beforeSave'] = function ($resource) {
                $resource->password = Hash::make($resource->password);
            };
        }
        return $this->update($id, $config);
    }

    /**
     * Returns an array containing field info for the change
     * password form. By default, this includes two fields.
     * password & password_confirmation.
     *
     * password_confirmation is a pseudo field just used to
     * validate the new password. It is not tied to a db field
     *
     * @return array
     */
    protected function getPasswordFormFields()
    {
        // get current rules assigned to the password property
        // our confirmation property will also need to match these rules
        $rules = $this->resource->getPropery('password')->validation;

        // add 'confirmed' rule to password - so it must match the new field
        $this->resource->addRule('password', 'confirmed');

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
     * Return a custom validation rule array for fields in the password
     * form. Both fields should share rules assigned to the 'password'
     * property. In addition, we add the 'confirmed' rule to password
     * so it matches out pusuedo field 'password_confirmation'
     *
     * @return array
     */
    protected function getPasswordFormRules()
    {
        $passwordRules = $this->resource->getPropery('password')->validation;
        $confirmationRules = $passwordRules;
        $passwordRules[] = 'confirmed';

        return [
            'password'              => $passwordRules,
            'password_confirmation' => $confirmationRules
        ];
    }
}
