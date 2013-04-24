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

use Input;
use Stwt\GoodForm\GoodForm as GoodForm;
use Request;
use Redirect;
use Session;
use URL;
use Validator;
use View;
use Log;

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

    protected $resource;

    protected $related;

    public $columns;

    /**
     * Default Actions in this controller
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

        if (Request::segment(3) != 'index' AND Request::segment(3)) {
            $this->breadcrumbs[Request::segment(2)] = $this->resource->plural();
        }
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
        error_log($method.' called');
        throw new NotFoundHttpException;
    }

    /**
     * Construct a paginated table of all resources in the database
     *
     * @return  view
     **/
    public function index($model = null, $modelId = null)
    {
        if ($model && $modelId) {
            $resource = $model::find($modelId)->images()->paginate();
            $this->related[$model] = $modelId;
        } else {
            $resource   = $this->resource->paginate(15);
        }
        $columns    = $this->resource->getColumns($this->columns);

        $controller = Request::segment(2);
        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->breadcrumbs['active'] = $this->resource->plural();

        $createUri    = 'admin/'.$controller.'/create';
        if ($this->related) {
            foreach ($this->related as $relatedModel => $relatedId) {
                $createUri .= '/'.$relatedModel.'/'.$relatedId;
            }
        }
        $createButton = Mothership::button($createUri, $singular, 'create');

        $data = [
            'breadcrumbs'    => $this->breadcrumbs,
            'resource'       => $resource,
            'title'          => 'All '.$plural,
            'createButton'   => $createButton,
            'controller'     => $controller,
            'columns'        => $columns,
            'singular'       => $singular,
            'plural'         => $plural,
        ];

        return View::make('mothership::resource.table')
            ->with($data)
            ->with($this->getTemplateData());
    }

    /**
     * Construct a form view to add a new resource to the database
     *
     * @return  view
     **/
    public function create($model = null, $modelId = null)
    {
        if ($model && $modelId) {
            $this->related[$model] = $modelId;
        }
        $action = $this->getAction('create', 'create');
        $fields = (isset($action['fields']) ? $action['fields'] : []);
        $fields = $this->resource->getFields($fields);

        $controller = Request::segment(2);
        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();
        $title      = 'Create '.$singular;

        $this->breadcrumbs['active'] = 'Create';

        $form   = new GoodForm();
        $form->add(['type' => 'hidden', 'name' => '_method', 'value' => 'POST']);

        foreach ($fields as $name => $field) {
            $form->add($field);
        }
        
        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        $formAttr = [
            'action'    => $this->getResourceUrl($controller),
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => true,
            'controller'    => $controller,
            'fields'        => $fields,
            'form'          => $form,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
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
    public function store($model = null, $modelId = null)
    {
        if ($model && $modelId) {
            $this->related[$model] = $modelId;
        }

        $inputData  = $this->getInputData(Input::all(), $this->resource);
        $fields     = $this->resource->getFields();
        $rules      = ($inputData ? $this->resource->getRules(array_keys($inputData)) : []);
        
        $controller = Request::segment(2);
        $singular   = $this->resource->singular();

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            $messages = $validation->messages();
            Messages::add('error', 'Please correct form errors.');
            
            return Redirect::to('admin/'.$controller.'/create')
                ->withInput()
                ->withErrors($validation);
        } else {
            foreach ($fields as $field => $spec) {
                $this->resource->$field = Input::get($field);
            }
            if ($this->resource->save()) {
                Messages::add('success', 'Created '.$singular);
                return Redirect::to('admin/'.$controller);
            }
            return Redirect::to('admin/'.$controller.'/create')
                ->withInput();
        }
    }

    /**
     * Construct a readonly view of a resource in the database
     *
     * @param int $id the resource id
     *
     * @return  view
     **/
    public function show($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $fields     = $this->resource->getFields();
        $title      = 'Update '.$singular.':'.$this->resource;

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

        return View::make('mothership::resource.meta')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
    }

    /**
     * Construct a form view to update a resource in the database
     *
     * @param int $id the resource id
     *
     * @return  view
     **/
    public function edit($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $fields     = $this->resource->getFields();
        $title      = 'Edit '.$singular.':'.$this->resource;

        $this->breadcrumbs['active'] = 'Update';

        $form   = new GoodForm();
        $form->add(['type' => 'hidden', 'name' => '_method', 'value' => 'PUT']);

        foreach ($fields as $name => $field) {
            $field->value = $this->resource->{$name};
            $form->add($field);
        }

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        $formAttr = [
            'action'    => URL::to('admin/'.$controller.'/'.$id),
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => false,
            'controller'    => $controller,
            'fields'        => $fields,
            'form'          => $form,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData())
            ->with('action_tabs', $this->getTabs());
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
     * @param int $id the resource id
     *
     * @return   void    (redirect) 
     **/
    public function delete($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $title = 'Delete '.$singular.':'.$this->resource;

        $this->breadcrumbs['active'] = 'Delete';

        $form = new GoodForm();

        $form->add(
            [
                'type'  => 'hidden',
                'name'  => '_method',
                'value' => 'DELETE',
            ]
        );
        $form->add(
            [
                'label' => 'Confirm Delete',
                'type'  => 'checkbox',
                'name'  => '_delete',
                'value' => $id,
            ]
        );

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        $formAttr = [
            'action'    => URL::to('admin/'.$controller.'/'.$id),
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => false,
            'controller'    => $controller,
            'form'          => $form,
            'resource'      => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
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
     * @param int $id the resource id
     *
     * @return   void    (redirect) 
     **/
    public function update($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);
        
        $redirect = 'admin/'.$controller.'/'.$id.'/edit';

        $inputData  = $this->getInputData(Input::all(), $this->resource);
        $fields     = $this->resource->getFields();
        $rules      = ($inputData ? $this->resource->getRules(array_keys($inputData)) : []);

        $validation = Validator::make($inputData, $rules);
        
        if ($validation->fails()) {
            $messages = $validation->messages();
            Messages::add('error', 'Please correct form errors.');
            return Redirect::to($redirect)
                ->withInput()
                ->withErrors($validation);
        } else {
            foreach ($fields as $field => $spec) {
                // only update field if it has changed
                $this->resource->$field = Input::get($field);
            }
            if ($this->resource->save()) {
                Messages::add('success', 'Updated '.$singular.':'.$this->resource);
                return Redirect::to($redirect);
            }
            return Redirect::to($redirect)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id the resource id
     *
     * @return  void    (redirect)
     **/
    public function destroy($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        $this->redirectIfDontExist($this->resource, $singular);

        $redirect = 'admin/'.$controller.'/'.$id.'/delete';

        $rules = ['_delete' => ['required', 'in:'.$id]];

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails()) {
            Messages::add('error', 'Please correct form errors.');
            return Redirect::to($redirect)->withErrors($validation);
        } else {
            if ($this->resource->delete()) {
                Messages::add('error', $singular.' Deleted.');
                return Redirect::to('admin/'.$controller);
            }
            Message::add('error', 'Error deleting '.$singular);
            return Redirect::to($redirect);
        }
        
        $redirect = 'admin/'.$controller.'/'.$id.'/edit';
    }

    /**
     * Called by router when requested method does 
     * not exist in the class
     *
     * @param array $parameters of requested methods arguments
     *
     * @return string
     **/
    public function missingMethod($parameters)
    {
        return 'Missing method';
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
     * Returns and associative array of values in $input
     * that are empty or have changed and are properties/columns 
     * of the $resource database table.
     *
     * If we just try to update all posted fields the 'unique'
     * validation rules will kick off.
     *
     * @param object $input    associative array of input data
     * @param object $resource the resource to update
     *
     * @return   array
     **/
    protected function getInputData($input, $resource)
    {
        $data = [];
        foreach ($input as $k => $v) {
            // add if $v is empty or has changed value
            if ((!$v OR $resource->$k != $v) AND $resource->isProperty($k)) {
                // only update field if it has changed
                $data[$k] = $v;
            }
        }
        return $data;
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
        $actions = $this->getActions(['update', 'related', 'create']);
        foreach ($actions as $key => $action) {
            $route = $action['uri'];
            // replace {controller} with current controller uri segment
            $uri = str_replace('{controller}', Request::segment(2), $route);
            
            if ($this->resource->id) {
                // replace {id} with current resource id
                $uri = str_replace('{id}', $this->resource->id, $route);
            } else {
                // if this action has no current resource disable the action
                $uri = (strpos($route, '{id}') === false ? $route : null);
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
     * - update
     * - create
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
     * i.e. getAction('view', 'update');
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
