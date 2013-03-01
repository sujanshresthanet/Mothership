<?php namespace Stwt\Mothership;

use GoodForm;
use Request;
use Session;
use URL;
use View;

class MothershipFileController extends MothershipResourceController
{
    public function create()
    {
        $fields     = $this->resource->getFields(['filename']);

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

        $errors = Session::get('errors');
        if ($errors) {
            $form->addErrors($errors->getMessages());
        }

        $formAttr = [
            'action'    => URL::to('admin/'.$controller),
            'class'     => 'form-horizontal',
            'enctype'   => 'multipart/form-data',
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
            ->with($this->getTemplateData());
    }
}
