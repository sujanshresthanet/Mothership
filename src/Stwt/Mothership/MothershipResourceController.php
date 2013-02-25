<?php namespace Stwt\Mothership;

use Input;
use GoodForm;
use Messages;
use Request;
use Redirect;
use URL;
use Validator;
use View;
use Log;

class MothershipResourceController extends MothershipController {

    static $model;

    protected $resource;

    public $columns;

    public function __construct()
    {
        parent::__construct();

        $class = static::$model;
        $this->resource = new $class;

        if ( Request::segment(3) != 'index' AND Request::segment(3) )
        {
            $this->breadcrumbs[Request::segment(2)] = $this->resource->plural();
        }
    }

   /*
    * Construct a paginated table of all resources in the database
    */
    public function index()
    {
        $resource   = $this->resource->paginate(15);
        $columns    = $this->resource->getColumns($this->columns);

        $controller = Request::segment(2);
        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->breadcrumbs['active'] = $this->resource->plural();

        $createButton = '<a class="btn btn-success pull-right" href="'.URL::to('admin/'.$controller.'/create').'"><i class="icon-white icon-plus"></i> '.$singular.'</a>';

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

   /*
    * Construct a form view to add a new resource to the database
    */
    public function create()
    {
        $fields     = $this->resource->getFields();

        $controller = Request::segment(2);
        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();
        $title      = 'Create '.$singular;

        $this->breadcrumbs['active'] = 'Create';

        $form   = new GoodForm();
        $form->add(['type' => 'hidden', 'name' => '_method', 'value' => 'POST']);

        foreach ($fields as $name => $field) {
            $field->value = $this->resource->{$name};
            $form->add($field);
        }

        $formAttr = [
            'action'    => URL::to('admin/'.$controller),
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => TRUE,
            'controller'    => $controller,
            'fields'        => $fields,
            'form'          => $form,
            'obj'           => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData());
    }

   /*
    * Attempt to store a new resource in the database
    */
    public function store()
    {
        $fields = $this->resource->getFields();
        $rules  = $this->resource->getRules();
        
        $controller = Request::segment(2);
        $singular   = $this->resource->singular();

        $validation = Validator::make(Input::all(), $rules);

        if ($validation->fails())
        {
            //Log::error(print_r($messages->all(), 1));
            $messages = $validation->messages();
            Messages::add('error', 'Please correct form errors.');
            
            return Redirect::to('admin/'.$controller.'/create')
                ->withInput()
                ->withErrors($validation);
        }
        else 
        {
            foreach ($fields as $field => $spec)
            {
                $this->resource->$field = Input::get($field);
            }
            if ($this->resource->save())
            {
                Messages::add('success', 'Created '.$singular);
                return Redirect::to('admin/'.$controller);
            }
            return Redirect::to('admin/'.$controller.'/create')
                ->withInput();
        }
    }

   /*
    * Construct a form view to update a resource from the database
    */
   public function edit($id)
   {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        if( !$this->resource )
        {
            Messages::add('warning', $singular.' with id '.$id.' not found.');
            return Redirect::to('admin/'.$controller);
        }

        $fields     = $this->resource->getFields();
        $title      = 'Update '.$singular.':'.$this->resource;

        $this->breadcrumbs['active'] = 'Create';

        $form   = new GoodForm();
        $form->add(['type' => 'hidden', 'name' => '_method', 'value' => 'PUT']);

        foreach ($fields as $name => $field) {
            $field->value = $this->resource->{$name};
            $form->add($field);
        }

        $formAttr = [
            'action'    => URL::to('admin/'.$controller.'/'.$id),
            'class'     => 'form-horizontal',
            'method'    => 'POST',
        ];
        $form->attr($formAttr);

        $data   = [
            'create'        => TRUE,
            'controller'    => $controller,
            'fields'        => $fields,
            'form'          => $form,
            'obj'           => $this->resource,
            'plural'        => $plural,
            'singular'      => $singular,
            'title'         => $title,
        ];

        return View::make('mothership::resource.form')
            ->with($data)
            ->with($this->getTemplateData());
   }

   /*
    * Attempt to update a resource from the database
    */
    public function update($id)
    {
        $class      = static::$model;
        $controller = Request::segment(2);

        $plural     = $this->resource->plural();
        $singular   = $this->resource->singular();

        $this->resource = $class::find($id);

        if( !$this->resource )
        {
            Messages::add('warning', $singular.' with id '.$id.' not found.');
            return Redirect::to('admin/'.$controller);
        }

        $fields = $this->resource->getFields();
        $rules  = $this->resource->getRules();
        
        $redirect = 'admin/'.$controller.'/'.$id.'/edit';

        $validation = Validator::make(Input::all(), $rules);
        
        if ($validation->fails())
        {
            $messages = $validation->messages();
            Messages::add('error', 'Please correct form errors.');
            
            return Redirect::to($redirect)
                ->withInput()
                ->withErrors($validation);
        }
        else 
        {
            foreach ($fields as $field => $spec)
            {
                $this->resource->$field = Input::get($field);
            }
            if ($this->resource->save())
            {
                Messages::add('success', 'Updated '.$singular.':'.$this->resource);
                return Redirect::to($redirect);
            }
            return Redirect::to($redirect)->withInput();
        }
    }
}