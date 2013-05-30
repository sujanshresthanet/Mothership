<?php
/**
 * MothershipResourceController.php
 *
 * PHP version 5.4.x
 *
 * @category MothershipResourceController
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */

namespace Stwt\Mothership;

use DB;
use Input;
use Lang;
use Log;
use URL;
use Request;
use Redirect;
use Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException as NotFoundHttpException;
use Stwt\GoodForm\GoodForm as GoodForm;
use Validator;
use View;

/**
 * MothershipResourceController
 *
 * The resource controller handles all the common CRUD actions in the mothership.
 *
 * @category Controller
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */
class MothershipResourceController extends MothershipController
{
    public static $model;

    protected $controller;
    protected $method;
    protected $requestor;

    protected $resource;

    protected $related;

    // defines what sort of request we are runing
    // collection, resource or single
    protected $actionType;

    public $columns;

    /**
     * Default Actions in this controller
     */
    public $actions = [
        'collection' => [
            'index' => [
                'label' => 'All',
                'uri' => '{controller}/index',
            ],
        ],
        'single' => [
            'create' => [
                'label' => 'Add',
                'uri' => '{controller}/create',
            ],
        ],
        'resource' => [
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
    ];


    /*
     * Construct the class, initialise the global resource instance
     *
     * @return   void
     **/
    public function __construct()
    {
        parent::__construct();
        $class = static::$model;
        $this->resource = new $class;

        $this->parseRequestUri();        
    }

    /*
     * Store key values from the uri like the controller and method.
     *
     *
     */
    protected function parseRequestUri()
    {
        $this->controller = $this->getUriController();
        $this->method     = $this->getUriMethod();
        $this->actionType = $this->getUriActionType($this->method);
        $this->related    = $this->getUriRelated();
        
        $this->requestor  = Input::get('_requestor');
        
        if (Request::segment(3) != 'index' AND Request::segment(3)) {
            $this->breadcrumbs[$this->controller] = $this->resource->plural();
        }
    }

    /*
     * Returns the controller uri segment
     *
     * @return string
     */
    protected function getUriController()
    {
        return Request::segment(2);
    }

    /*
     * Returns the method uri segment. We follow the following rules to define the method:
     *
     * 1. First look in the forth segment after the id e.g. /admin/controller/37/method
     * 2. Use the uri segment after the controller if it is not an id
     * 3. If we have a third segment (id) this must be the show method
     * 4. If we have no third segment this must be the index
     *
     * @return string
     */
    protected function getUriMethod()
    {
        $method = Request::segment(4);
        if (!$method) {
            if (is_string(Request::segment(3))) {
                $method = Request::segment(3);
            } else {
                $method = (Request::segment(3) ? 'show' : 'index');
            }
        }
        return $method;
    }

    /**
     * Return the type of action the current request is.
     *
     * @param string $method
     *
     * @return string
     */
    protected function getUriActionType($method)
    {
        $types = ['resource', 'collection', 'single'];

        foreach ($types as $type) {
            $actions = $this->getActions($type);
            if (isset($actions[$method])) {
                return $type;
            }
        }
        return 'collection';
    }

    /**
     * Looks at the Uri and checks if we have any related resources defined
     * Depending on the type of action this is, the realted model and id will
     * either begin in the fourth or fifth segment.
     * e.g.
     * - collection = /admin/controller/index/relatedModel/relatedId
     * - single     = /admin/controller/create/relatedModel/relatedId
     * - resource   = /admin/controller/65/edit/relatedModel/relatedId
     *
     * @param array;
     */
    protected function getUriRelated()
    {
        $offset = ($this->actionType == 'resource' ? 5 : 4 );
        $related = [];
        if (Request::segment($offset + 1)) {
            $relatedModel = Request::segment($offset);
            $relatedId    = Request::segment($offset + 1);
            $related[] = [
                'model' => $relatedModel,
                'id'    => $relatedId,
            ];
        }
        return $related;
    }

    /*
     * Called if method does not exist
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        throw new NotFoundHttpException;
    }

    /**
     * Construct a paginated table of all resources in the database
     *
     * @return  view
     **/
    public function index($config = null)
    {
        $resource   = $this->queryRelated($this->resource);
        $paginator  = $resource->orderBy('created_at', 'desc')->paginate(15);

        $queries = DB::getQueryLog();
        $lastQuery = end($queries);
        Log::error('##########');
        Log::error(print_r($queries, 1));

        $title   = $this->getTitle($this->resource, $config, 'index');
        $caption = 'Displaying all '.$this->resource->plural();
        $columns = $this->resource->getColumns($this->columns);

        $this->breadcrumbs['active'] = $this->resource->plural();

        $createUri    = 'admin/'.$this->controller.'/create';
        
        $createButton = Mothership::button($createUri, $this->resource->singular(), 'create');

        $data = [
            'breadcrumbs'    => $this->breadcrumbs,
            'resource'       => $this->resource,
            'paginator'      => $paginator,
            'title'          => $title,
            'caption'        => $caption,
            'columns'        => $columns,
            'controller'     => $this->controller,
            'createButton'   => $createButton,
        ];

        return View::make('mothership::resource.table')
            ->with($data)
            ->with($this->getTemplateData());
    }

    /*
     * Checks if the current request is for related resources and
     * adds that relationship clause to the query
     *
     * @param object $resource
     *
     * @return object
     */
    protected function queryRelated($resource)
    {
        if ($this->isRelatedRequest()) {
            $model = $this->related[0]['model'];
            $id = $this->related[0]['id'];

            $foreignKey = $model.'_id';
            if ($resource->hasProperty($foreignKey)) {
                Log::error('Query related '.get_class($resource));
                return $resource->where($foreignKey, '=', $id);
                Log::error('Query related '.get_class($return));
            }
        }
        return $resource;
    }

    /**
     * Returns true if this request is an related request
     *
     * @return boolean
     */
    public function isRelatedRequest()
    {
        return $this->related ? true : false;
    }

    /**
     * Construct a form view to add a new resource to the database
     *
     * @return  view
     **/
    public function create($config = [])
    {
        $fields = $this->getFields($this->resource, $config);
        $title  = $this->getTitle($this->resource, $config, 'create');

        $this->breadcrumbs['active'] = 'Create';

        // start building the form
        $form   = new GoodForm();
        
        // add field to store request type
        $methodField = ['type' => 'hidden', 'name' => '_method', 'value' => 'POST'];
        $form->add($methodField);
        
        // This is the route a failed form will redirect to
        $form->add(['type' => 'hidden', 'name' => '_redirect', 'value' => Request::url()]);

        // This is the route a succesfull form will redirect to
        $form->add(
            [
                'type' => 'hidden',
                'name' => '_redirect_success',
                'value' => URL::to('admin/'.$this->controller),
            ]
        );

        // add field to store the method that submitted the form
        $redirectField = ['type' => 'hidden', 'name' => '_requestor', 'value' => $this->method];
        $form->add($redirectField);

        foreach ($fields as $name => $field) {
            if ($this->resource->hasProperty($name) AND isset($this->resource->{$name})) {
                // check if this field is a property - it probably
                // wont but we may have pre assigned values
                $field->value = $this->resource->{$name};
            }
            $form->add($field);
        }

        // Add form actions
        $form->addAction(
            [
                'class' => 'btn btn-primary',
                'form'  => 'button',
                'name'  => '_save',
                'type'  => 'submit',
                'value' => Arr::e($config, 'submitText', 'Save'),
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

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        // generate the form action - default to "admin/controller"
        $action = Arr::e($config, 'action', URL::to('admin/'.$this->controller));

        $formAttr = [
            'action'    => $action,
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => false,
            'controller'    => $this->controller,
            'fields'        => $fields,
            'form'          => $form,
            'resource'      => $this->resource,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Attempt to store a new resource in the database
     *
     * @return void (redirects)
     **/
    public function store($config = [])
    {
        $data   = Input::all();
        $fields = array_keys($data);
        $rules  = $this->getRules($this->resource, $fields, $config);
        $data   = $this->filterInputData($this->resource, $data, array_keys($rules));
        
        $validation = Validator::make($data, $rules);
        
        if ($validation->fails()) {
            $messages = $validation->messages();
            $message = $this->getAlert($this->resource, 'error', $config, 'create');
            Messages::add('error', $message);
            return Redirect::to(Input::get('_redirect'))
                ->withInput()
                ->withErrors($validation);
        } else {
            foreach ($fields as $field) {
                if ($this->resource->hasProperty($field) AND Input::get($field)) {
                    $this->resource->$field = Input::get($field);
                }
            }
            if (Arr::e($config, 'beforeSave')) {
                $callback = Arr::e($config, 'beforeSave');
                $callback($this->resource);
            }
            if ($this->resource->save()) {
                $message = $this->getAlert($this->resource, 'success', $config, 'create');
                Messages::add('success', $message);
            }
            if (Arr::e($config, 'afterSave')) {
                $callback = Arr::e($config, 'afterSave');
                $callback($this->resource);
            }
            return Redirect::to(Input::get('_redirect_success'));
        }
    }

    /**
     * Construct a readonly view of a resource in the database
     *
     * @param int   $id
     * @param array $config
     *
     * @return  view
     **/
    public function show($id, $config = [])
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $fields = $this->resource->getFields();
        $title  = $this->getTitle($this->resource, $config);

        $this->breadcrumbs['active'] = 'View';

        $data   = [
            'create'        => false,
            'controller'    => $controller,
            'fields'        => $fields,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.view')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Return a view containing all the meta data for this model
     *
     * @param int $id the resource id
     *
     * @return view
     **/
    public function meta($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $fields     = $this->resource->getFields();
        $title      = 'Meta '.$singular.':'.$this->resource;

        $this->breadcrumbs['active'] = 'Meta';

        $data   = [
            'create'        => false,
            'controller'    => $controller,
            'fields'        => $fields,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.meta')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Construct a form view to update a resource in the database
     *
     * @param int   $id     the resource id
     * @param array $config override defaults in the edit view
     *
     * @return  view
     **/
    public function edit($id, $config = [])
    {
        $this->resource = $this->getResource($id);

        $fields = $this->getFields($this->resource, $config);
        $title  = $this->getTitle($this->resource, $config);

        $this->breadcrumbs['active'] = 'Edit';

        // start building the form
        $form   = new GoodForm();
        
        // add field to store request type
        $methodField = ['type' => 'hidden', 'name' => '_method', 'value' => 'PUT'];
        $form->add($methodField);
        
        // add field to store url to redirect back to
        $redirectField = ['type' => 'hidden', 'name' => '_redirect', 'value' => Request::url()];
        $form->add($redirectField);

        // add field to store url to redirect back to on success
        $redirectSuccessField = ['type' => 'hidden', 'name' => '_redirect_success', 'value' => Request::url()];
        $form->add($redirectSuccessField);

        // add field to store the method that submitted the form
        $redirectField = ['type' => 'hidden', 'name' => '_requestor', 'value' => $this->method];
        $form->add($redirectField);

        foreach ($fields as $name => $field) {
            if ($this->resource->hasProperty($name)) {
                // check if this field is a property
                $field->value = $this->resource->{$name};
            }
            $form->add($field);
        }

        // Add form actions
        $form->addAction(
            [
                'class' => 'btn btn-primary',
                'form'  => 'button',
                'name'  => '_save',
                'type'  => 'submit',
                'value' => Arr::e($config, 'submitText', 'Save'),
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

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        // generate the form action - default to "admin/controller/id"
        $action = Arr::e($config, 'action', URL::to('admin/'.$this->controller.'/'.$id));

        $formAttr = [
            'action'    => $action,
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => false,
            'controller'    => $this->controller,
            'fields'        => $fields,
            'form'          => $form,
            'resource'      => $this->resource,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Attempt to update a resource from the database
     *
     * @param int   $id     the resource id
     * @param array $config override default update of form
     *
     * @return   void    (redirect) 
     **/
    public function update($id, $config = [])
    {
        $this->resource = $this->getResource($id);

        $data   = Input::all();
        $fields = array_keys($data);
        $rules  = $this->getRules($this->resource, $fields, $config);
        $data   = $this->filterInputData($this->resource, $data, array_keys($rules));
        
        $validation = Validator::make($data, $rules);
        
        if ($validation->fails()) {
            $messages = $validation->messages();
            $message = $this->getAlert($this->resource, 'error', $config);
            Messages::add('error', $message);
            return Redirect::to(Input::get('_redirect'))
                ->withInput()
                ->withErrors($validation);
        } else {
            foreach ($fields as $field) {
                if ($this->resource->hasProperty($field) AND Input::get($field)) {
                    $this->resource->$field = Input::get($field);
                }
            }
            if (Arr::e($config, 'beforeSave')) {
                $callback = Arr::e($config, 'beforeSave');
                $callback($this->resource);
            }
            if ($this->resource->save()) {
                $message = $this->getAlert($this->resource, 'success', $config);
                Messages::add('success', $message);
            }
            if (Arr::e($config, 'afterSave')) {
                $callback = Arr::e($config, 'afterSave');
                $callback($this->resource);
            }
            return Redirect::to(Input::get('_redirect_success'));
        }
    }

    /**
     * Construct a view displaying the resources update history
     *
     * @param int $id the resource id
     *
     * @return  view
     **/
    public function history($id)
    {
        
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $fields     = $this->resource->getFields();
        $title      = 'View '.$singular.' History:'.$this->resource;

        $this->breadcrumbs['active'] = 'History';

        $data   = [
            'create'        => false,
            'controller'    => $controller,
            'fields'        => $fields,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.history')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Create a confirm delete view
     *
     * @param int   $id the resource id
     * @param array $config override defaults in the edit view
     *
     * @return   void    (redirect) 
     **/
    public function delete($id, $config = [])
    {
        
        $this->resource = $this->getResource($id);

        $title  = $this->getTitle($this->resource, $config);

        $this->breadcrumbs['active'] = 'Delete';

        // start building the form
        $form   = new GoodForm();
        
        // add field to store request type
        $form->add(['type' => 'hidden', 'name' => '_method', 'value' => 'DELETE']);
        // add field to store url to redirect back to
        $form->add(
            [
                'type' => 'hidden',
                'name' => '_redirect',
                'value' => Request::url(),
            ]
        );
        // add field to store url to redirect to on success
        $form->add(
            [
                'type' => 'hidden',
                'name' => '_redirect_success',
                'value' => URL::to('admin/'.$this->controller),
            ]
        );
        // add field to store the method that submitted the form
        $form->add(['type' => 'hidden', 'name' => '_requestor', 'value' => $this->method]);

        // Confirm delete checkbox
        $form->add(
            [
                'label' => 'Confirm Delete',
                'type'  => 'checkbox',
                'name'  => '_delete',
                'value' => $id,
            ]
        );

        // Add form actions
        $form->addAction(
            [
                'class' => 'btn btn-danger',
                'form'  => 'button',
                'name'  => '_save',
                'type'  => 'submit',
                'value' => Arr::e($config, 'submitText', 'Delete'),
            ]
        );

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        // generate the form action - default to "admin/controller/id"
        $action = Arr::e($config, 'action', URL::to('admin/'.$this->controller.'/'.$id));

        $formAttr = [
            'action'    => $action,
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => false,
            'form'          => $form,
            'resource'      => $this->resource,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int   $id     the resource id
     * @param array $config override default destroying of resource
     *
     * @return  void    (redirect)
     **/
    public function destroy($id, $config = [])
    {
        $this->resource = $this->getResource($id);
        // custom rules for confirm delete checkbox
        $rules = [
            '_delete' => ['required', 'in:'.$id]
        ];
        $messages = ['required' => 'Please check the box to confirm you want to delete this record.'];
        $validation = Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            $message = $this->getAlert($this->resource, 'error', $config);
            Messages::add('error', $message);
            return Redirect::to(Input::get('_redirect'))->withErrors($validation);
        } else {
            $this->resource->delete();
            $message = $this->getAlert($this->resource, 'success', $config);
            Messages::add('success', $message);
            return Redirect::to(Input::get('_redirect_success'));
        }
    }

    /**
     * Loads an instance of the resource model by id. This method will
     * throw a NotFoundHttpException if no instance is found by default
     *
     * @param int     $id
     * @param boolean $throwExceptionIfNotFound
     *
     * @return object
     */
    protected function getResource($id, $throwExceptionIfNotFound = true)
    {
        $class = static::$model;
        $resource = $class::find($id);
        if (!$resource->exists() AND $throwExceptionIfNotFound) {
            throw new NotFoundHttpException;
        }
        return $resource;
    }

    /**
     * Returns an array of field models/arrays for a form.
     * By default the method will return all fields in the
     * resource. If $config['fields'] is set, these fields
     * wil be returned instead.
     *
     * $config['fields'] can contain an array of field names,
     * and array of field specifications or both.
     *
     * @param object $resource
     * @param array  $config
     *
     * @return array
     */
    protected function getFields($resource, $config)
    {
        $fields = Arr::e($config, 'fields', null);
        return $resource->getFields($fields);
    }

    /**
     * Returns validation rules for form fields.
     *
     * By default, return field rules in the resource for properties
     * in the $fields paramenter. Rules can be overrided in $config['rules']
     *
     * @param object $resource
     * @param array  $fields
     * @param array  $config
     *
     * @return array
     */
    protected function getRules($resource, $fields, $config = [])
    {
        $rules = $resource->getRules(Arr::e($config, 'rules', $fields));
        return $rules;
    }

    /**
     * Returns a localized string that will be used as the page and
     * browser title.
     *
     * Strings are defined in the mothership language file.
     * There are a number of ways to define which string is returned.
     *
     * Custom:
     * In the $config array. Add the language path to $config['title']
     *
     * Auto:
     * If you have a custom edit page controller/{id}/password
     * this method will look to see if the language line
     * titles.password exists and return that.
     *
     * Generic:
     * The default option is to return one of the generic alert messages.
     * These are defined in the language line titles.{fallback}
     *
     * @param object $resource
     * @param array  $config
     * @param string $fallback
     *
     * @return string
     */
    protected function getTitle($resource, $config = [], $fallback = 'edit')
    {
        $page   = $this->method;
        // look for custom language key in the config
        $key = Arr::e($config, 'title');
        if (!$key) {
            // then look for a language item based on the current page
            $key = 'titles.'.$page;
        }
        if (!$this->hasLang($key)) {
            // finally fallback to a generic message [update or create]
            $key = 'titles.'.$fallback;
        }
        return $this->getLang($key, $resource);
    }

    /**
     * Returns a localized string that will be displayed to the
     * user after they have completed an action.
     *
     * Strings are defined in the mothership language file.
     * There are a number of ways to define which string is returned.
     *
     * Custom:
     * In the $config array. If $type='update' add the language path
     * to $config['updateAlert']
     *
     * Auto:
     * If you have a custom edit page controller/{id}/details
     * this method will look to see if the language line
     * alerts.details.{type} exists and return that.
     *
     * Generic:
     * The default option is to return one of the generic alert messages.
     * These are defined in the language line  alerts.{fallback}.{type}
     *
     * @param object $resource
     * @param string $type
     * @param array  $config
     * @param string $fallback
     *
     * @return string
     */
    protected function getAlert($resource, $type, $config = [], $fallback = 'edit')
    {
        // look for custom language key in the config
        $key = Arr::e($config, $type.'Alert');
        if (!$key) {
            // then look for a language item based on the requesting (get) method
            $key = 'alerts.'.$this->requestor.'.'.$type;
        }
        if (!$this->hasLang($key)) {
            // finally fallback to a generic message [update or create]
            $key = 'alerts.'.$fallback.'.'.$type;
        }
        return $this->getLang($key, $resource);
    }

    /**
     * Returns a language line replacing any place-holder
     * strings with $resource specific values
     *
     * @param string $key
     * @param object $resource
     *
     * @return string
     */
    private function getLang($key, $resource)
    {
        $prefix = 'mothership::mothership.';
        $placeHolders = [
            'singular'  => $resource->singular(),
            'plural'    => $resource->plural(),
            'resource'  => $resource->__toString(),
        ];
        return Lang::get($prefix.$key, $placeHolders);
    }

    /**
     * Checks if this app has a given language line
     *
     * @param string $key
     *
     * @return boolean
     */
    private function hasLang($key)
    {
        $prefix = 'mothership::mothership.';
        return Lang::has($prefix.$key);
    }

    /**
     * Returns and associative array of values in $input
     * that are empty or have changed and are properties/columns 
     * of the $resource database table.
     *
     * If we just try to update all posted fields the 'unique'
     * validation rules will kick off.
     *
     * @param object $resource - resource model instance
     * @param array  $input    - associative array of input data
     * @param array  $fields   - form items included in this form may
     *                           not even be properties of the resouce
     *
     * @return   array
     **/
    protected function filterInputData($resource, $input, $fields)
    {
        $data = [];
        foreach ($input as $k => $v) {
            // check if in the field array
            if (!in_array($k, $fields)) {
                Log::debug("$k skipped as not in fields array");
                continue;
            }
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * Redirects to listing page if the resource does not exists
     *
     * @param object $resource the object instance to check
     * @param string $singular singular object name for message
     *
     * @return  void
     **/
    public function redirectIfDontExist($resource, $singular)
    {
        if (!$this->resource) {
            $controller = Request::segment(2);
            Messages::add('warning', $singular.' with id '.$id.' not found.');
            return Redirect::to('admin/'.$controller);
        }
    }

    /**
     * Prepares the action navigation tabs for view rendering
     * We replace placeholder strings like {controller} & {id}
     * with their proper values
     *
     * @return array
     */
    protected function getTabs()
    {
        $array = [];
        $actions = $this->getActions(['resource', 'related', 'single']);
        foreach ($actions as $key => $action) {
            $route = $action['uri'];
            // replace {controller} with current controller uri segment
            $uri = str_replace('{controller}', Request::segment(2), $route);

            if ($this->resource->id) {
                // replace {id} with current resource id
                $uri = str_replace('{id}', $this->resource->id, $uri);
            } else {
                // if this action has no current resource disable the action
                $uri = (strpos($uri, '{id}') === false ? $uri : null);
            }

            if ($uri) {
                $uri = 'admin/'.$uri;
            }

            // add classes
            if (!isset($action['class'])) {
                $action['class'] = [];
            }
            if (Request::is($uri)) {
                $action['class'][] = 'active';
            }
            if (!$uri) {
                $action['class'][] = 'disabled';
            }

            $action['class'] = implode(' ', $action['class']);

            if ($uri) {
                $action['link'] = '<a href="'.URL::to($uri).'">'.$action['label'].'</a>';
            } else {
                $action['link'] = '<a>'.$action['label'].'</a>';
            }

            $array[$route] = $action;
        }
        return $array;
    }

    /**
     * Returns array of actions specified in this controller.
     * $type can either be a action group (string) or multiple
     * groups (array). Common action groups are as follows:
     * - collections
     * - single
     * - resource
     * - related
     *
     * @param string/array $type
     * @return array
     */
    protected function getActions($type)
    {
        if (is_string($type)) {
            return (isset($this->actions[$type]) ? $this->actions[$type] : []);
        } else {
            $actions = [];
            foreach ($type as $t) {
                $actions = array_merge($actions, $this->getActions($t));
            }
            return $actions;
        }
    }

    /**
     * Return an action array by key and group. If either
     * the key or group does not exist, an empty array is returned
     * i.e. getAction('view', 'resource');
     *
     * @param string $key
     * @param string $group
     *
     * @return array
     */
    public function getAction($key, $group)
    {
        if (isset($this->actions[$group]) && isset($this->actions[$group][$key])) {
            return $this->actions[$group][$key];
        }
        return [];
    }

    /*
     * Returns the full url to a resource action.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getResourceUrl($uri)
    {
        return URL::to($this->getResourceUri($uri));
    }

    /*
     * Returns the uri to a resource action.
     *
     * Takes a simple uri like controller/{id}/edit.
     * Suffixes the admin base uri and prefixes any related
     * uri information.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getResourceUri($uri)
    {
        $uri = 'admin/'.$uri;
        if ($this->related) {
            foreach ($this->related as $relatedModel => $relatedId) {
                $uri .= '/'.$relatedModel.'/'.$relatedId;
            }
        }
        return $uri;
    }
}
