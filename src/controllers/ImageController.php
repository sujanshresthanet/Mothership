<?php namespace Stwt\Mothership;

class ImageController extends FileController
{
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
                'label' => 'Upload',
                'uri' => '{controller}/create',
            ],
        ],
        'resource' => [
            'view' => [
                'label' => 'Preview',
                'uri' => '{controller}/{id}',
            ],
            'edit' => [
                'label' => 'Edit',
                'uri' => '{controller}/{id}/edit',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri' => '{controller}/{id}/delete',
            ],
        ],
        'related' => [
        ],
    ];

    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->columns = [
            'Image' => function ($image) {
                return $image->image();
            },
            'title',
            'created_at',
        ];
    }
}
