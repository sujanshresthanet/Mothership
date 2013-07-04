<?php namespace Stwt\Mothership;

class PageController extends NestedSetController
{
    /**
     * Default Action methods in this controller, also constructs the navigation
     * 
     * @var array
     */
    public $actions = [
        'collection' => [
            'index' => [
                'label' => 'All',
                'uri'   => '{controller}/index',
            ],
        ],
        'single' => [
            'create' => [
                'label' => 'Add',
                'uri'   => '{controller}/create',
            ],
        ],
        'resource' => [
            'edit' => [
                'label' => 'Detials',
                'uri'   => '{controller}/{id}:edit',
            ],
            'view' => [
                'label' => 'View',
                'uri'   => '{controller}/{id}',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri'   => '{controller}/{id}:delete',
            ],
        ],
        'related' => [
            'regions' => [
                'label' => 'Content',
                'uri'   => '{controller}/{id}/regions/index',
            ]
        ],
    ];
}
