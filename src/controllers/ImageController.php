<?php namespace Stwt\Mothership;

class ImageController extends FileController
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
            'edit' => [
                'label' => 'Edit',
                'uri' => '{controller}/{id}:edit',
            ],
            'view' => [
                'label' => 'View',
                'uri' => '{controller}/{id}',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri' => '{controller}/{id}:delete',
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
                return $image->thumbnail($image->title, ['width' => 120]);
            },
            'title',
            'created_at',
        ];
    }


    /**
     * Extend the store method to add a beforeSave callback
     * that will resize images to all defined dimesions.
     * 
     * @param  array $config
     * 
     * @return Redirect
     */
    public function store($config = [])
    {
       $config = Arr::s(
            $config,
            'afterSave',
            function ($image) {
                $image->initImage();
            }
        );

        return parent::store($config);
    }
}
