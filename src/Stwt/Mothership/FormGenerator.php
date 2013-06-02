<?php namespace Stwt\Mothership;

use Illuminate\Support\Facades\Input as Input;
use Stwt\GoodForm\GoodForm as GoodForm;
use Illuminate\Support\Facades\Log as Log;
use Illuminate\Support\Facades\Session as Session;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\URL as URL;
use Illuminate\Support\Facades\Validator as Validator;

class FormGenerator
{
    /**
     * The method of the generated form (POST, PUT, GET or DELETE)
     *
     * @var string
     */
    private $method = 'POST';

    /**
     * An instance of a model that this library will generate a form 
     * for or update from a form.
     *
     * @var object
     */
    private $resource;

    /**
     * An array of fields to add to the form
     *
     * @var array
     */
    private $fields;

    /**
     * An array of actions to add to the form footer
     * e.g. save & cancel buttons
     *
     * @var array
     */
    private $actions = [];

    /**
     * An array of rules for each field
     * 
     * @var array
     */
    private $rules = [];

    /**
     * Message displayed to user on form error
     * 
     * @var string
     */
    private $errorMessage;

    /**
     * Message displayed to user on form success
     * 
     * @var string
     */
    private $successMessage;

    /**
     * Was the form save successfully
     * 
     * @var boolean
     */
    private $formSuccess;

    /**
     * Reference to the validation instance used to validate the form
     * @var object
     */
    private $validation;

    /**
     * The URL to redirect to on success
     * 
     * @var URL
     */
    private $successRedirect;

    /**
     * The URL to redirect to on error
     * 
     * @var URL
     */
    private $errorRedirect;

    /**
     * Sets the resource object that we will build/update the form
     * Resources must extend from MothershipModel. If no fields have
     * been set yet, we'll use the resources default fields (all 
     * public fields)
     *
     * @param object $resource
     *
     * @return object
     */
    public static function resource($resource)
    {
        $instance = new FormGenerator();
        
        $instance->resource = $resource;
        if (empty($instance->fields)) {
            $instance->fields($resource->getFields());
        }
        return $instance;
    }

    /**
     * Sets the form method (POST, PUT, GET or DELETE)
     *
     * @param string $method
     *
     * @return object
     */
    public function method($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Defines what fields will be used in the form
     *
     * @param array $fields
     *
     * @return object
     */
    public function fields($fields)
    {
        $this->fields = $this->resource->getFields($fields);
        return $this;
    }

    /**
     * Adds multiple actions to the form
     *
     * true  - Save and cancel actions added to form (default)
     * false - No actions added to form
     * array - An array of fields to add to the form
     *
     * @param mixed $actions
     *
     * @return object
     */
    public function actions($actions)
    {
        if (is_bool($actions)) {
            if ($actions) {
                $this->addDefaultActions();
            } else {
                $this->actions = $actions;
            }
            return $this;
        }
        foreach ($actions as $action) {
            $this->action($action);
        }
        return $this;
    }

    /**
     * Adds a given action to the form
     *
     * @param array $action
     *
     * @return object
     */
    public function action($action)
    {
        $this->actions[] = $action;
        return $this;
    }

    /**
     * Adds a save button to the form footer
     *
     * - Pass a string to change the default save buttons text
     * - Pass an array to overwrite the default save button
     * - Pass null to add the default save button
     *
     * @param mixed $save
     *
     * @return object
     */
    public function saveButton($save = null)
    {
        $default = [
            'class' => 'btn btn-primary',
            'form'  => 'button',
            'name'  => '_save',
            'type'  => 'submit',
            'value' => 'Save',
        ];
        if (is_string($save)) {
            $action  = $default;
            $action['label'] = $save;
        } elseif (is_array($save)) {
            $action = array_merge($default, $save);
        } else {
            $action = $default;
        }
        return $this->action($action);
    }

    /**
     * Adds a cancel button to the form footer
     *
     * - Pass a string to change the default cancel buttons text
     * - Pass an array to overwrite the default cancel button
     * - Pass null to add the default cancel button
     *
     * @param mixed $save
     *
     * @return object
     */
    public function cancelButton($cancel = null)
    {
        $default = [
            'class' => 'btn',
            'form'  => 'button',
            'name'  => '_cancel',
            'type'  => 'reset',
            'value' => 'Cancel',
        ];
        if (is_string($cancel)) {
            $action  = $default;
            $action['label'] = $cancel;
        } elseif (is_array($cancel)) {
            $action = array_merge($default, $cancel);
        } else {
            $action = $default;
        }
        return $this->action($action);
    }

    /**
     * Build and return the instance of the GoodForm object
     *
     * @return object GoodForm
     */
    public function form()
    {
        $form = new GoodForm;

        $form->hidden('_method', $this->method);

        $this->addFieldsToForm($form);

        $this->addErrorsToForm($form);

        $this->addActionsToForm($form);

        return $form;
    }

    /**
     * Add rules for field in the form
     *
     * @param  array $rules
     * 
     * @return object
     */
    public function rules($rules)
    {
        $this->rules = array_merge($this->rules, $rules);
        return $this;
    }

    /**
     * Set the message displayed to users on error
     * 
     * @param string $message
     * 
     * @return object
     */
    public function errorMessage($message)
    {
        $this->errorMessage = $message;
        return $this;
    }

    /**
     * Set the message displayed to users on success
     * 
     * @param string $message
     * 
     * @return object
     */
    public function saveMessage($message)
    {
        $this->successMessage = $message;
        return $this;
    }

    /**
     * Sets the success redirect url.
     * $url Param can be a string or URL instance
     * 
     * @param  mixed $url
     * @return object
     */
    public function successRedirect($url)
    {
        $url = (is_string($url) ? URL::to($url) : $url);
        $this->successRedirect = $url;
        return $this;
    }

    /**
     * Sets the error redirect url.
     * $url Param can be a string or URL instance
     * 
     * @param  mixed $url
     * @return object
     */
    public function errorRedirect($url)
    {
        $url = (is_string($url) ? URL::to($url) : $url);
        $this->errorRedirect = $url;
        return $this;
    }

    /**
     * Attempts to update a model instance with input data.
     * If $input parameter is null we use data from Input::all()
     *
     * @param array  $input
     *
     * @return mixed
     */
    public function save($input = null)
    {
        $data = ($input ?: Input::all());

        $rules = $this->getRules();

        Log::error(print_r(array_keys($rules), 1));

        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            Messages::add('error', $this->errorMessage);
            $messages = $validation->messages();
            Log::error(print_r($messages, 1));

            $this->formSuccess = false;
            $this->validation = $validation;
        } else {
            $this->resource->update($data);
            $this->resource->save();

            Messages::add('success', $this->successMessage);
            $this->formSuccess = true;
        }

        return $this;
    }

    /**
     * Redirects requres to a the relevant url depending on the
     * status of the form update.
     * 
     * $url param will set both the success and error redirect 
     * urls. If different urls are required call errorRedirect
     * & successRedirect before.
     * 
     * @param  mixed $url
     * 
     * @return object
     */
    public function redirect($url = null)
    {
        if ($url) {
            $this->errorRedirect($url);
            $this->successRedirect($url);
        }

        if ($this->formSuccess === true) {
            return Redirect::to($this->successRedirect);
        } elseif ($this->formSuccess === false) {
            return Redirect::to($this->errorRedirect)
                ->withInput()
                ->withErrors($this->validation);
        }
    }

    /**
     * -------------------------------------------
     */

    /**
     * Adds form fields for the resource instance. Also check
     * if thie field is a property of the resource and set the
     * field value.
     *
     * @param GoodForm $form
     *
     * @return void
     */
    private function addFieldsToForm($form)
    {
        Log::error('#################');
        Log::error('Fields: '.print_r(array_keys($this->fields), 1));
        foreach ($this->fields as $name => $field) {
            if ($this->resource->isProperty($name)) {
                $field->value = $this->resource->{$name};
            }
            $form->add($field);
        }
    }

    /**
     * Adds any errors from the session to the form
     *
     * @param GoodForm $form
     *
     * @return void
     */
    private function addErrorsToForm($form)
    {
        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }
    }

    /**
     * Adds any actions to the form
     *
     * @param GoodForm $form
     *
     * @return void
     */
    private function addActionsToForm($form)
    {
        if ($this->actions === false) {
            return;
        }
        if (empty($this->actions)) {
            $this->addDefaultActions();
        }

        foreach ($this->actions as $action) {
            $form->addAction($action);
        }
    }

    /**
     * Adds the default save and cancel buttons to the form
     *
     * @return void
     */
    private function addDefaultActions()
    {
        $this->saveButton();
        $this->cancelButton();
    }

    /**
     * Return a rules array for all fields in the form
     * 
     * @return array
     */
    private function getRules()
    {
        $fields = array_keys($this->fields);
        return $this->resource->getRules();
    }
}
