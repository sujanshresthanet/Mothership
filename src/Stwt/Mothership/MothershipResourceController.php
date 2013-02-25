<?php namespace Stwt\Mothership;

use Request;
use URL;
use View;

class MothershipResourceController extends MothershipController {

    protected static $model;

    protected $resource;

    public $columns;

    public function __construct()
    {
        parent::__construct();

        $class = static::$model;
        $this->resource = new $class;
    }

    public function index()
    {
        $resource   = $this->resource->paginate(15);
        $columns    = $this->resource->getColumns($this->columns);

        $controller = Request::segment(1);
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

        return View::make('mothership::resource.table')->with($data)->with($this->getTemplateData());
    }

}