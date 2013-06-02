<?php namespace Stwt\Mothership;

use Log;
use Stwt\GoodForm\GoodForm as GoodForm;
use Session;

class FormGenerator
{
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
	 * The method of the generated form (POST, PUT, GET or DELETE)
	 *
	 * @var string
	 */
	private $method = 'POST';

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
		} else if (is_array($save)) {
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
		} else if (is_array($cancel)) {
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
	 * Attempts to update a model instance with Input data
	 *
	 * @param object $resource
	 *
	 * @return object
	 */
	public function update($resource)
	{
		# code...
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
}
