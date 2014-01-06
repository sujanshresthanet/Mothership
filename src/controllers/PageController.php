<?php namespace Stwt\Mothership;

class PageController extends ResourceController
{
    /**
     * The model resource this controller represents
     * 
     * @var string
     */
    public $model = 'Stwt\Mothership\PageModel';

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
            /*'content' => [
                'label' => 'Content',
                'uri'   => '{controller}/{id}:content'
            ],*/
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

    /**
     * Customise the fields on display for the create page
     * 
     * @param  array $config
     * 
     * @return View
     */
    public function create($config = [])
    {
        $config['fields'] = [
            'name',
            'status',
            'template',
            'page_id',
        ];

        return parent::create($config);
    }

    /**
     * Customise the fields on display for the edit page
     * 
     * @param  array $config
     * 
     * @return View
     */
    public function edit($id, $config = [])
    {
        $config['fields'] = [
            'name',
            'slug',
            'status',
            'template',
            'page_id',
        ];

        return parent::edit($id, $config);
    }

    public function show($id, $config = [])
    {
        $data = [];

        $this->before($config);

        // set default config variable for this view
        $this->setDefaults(
            $config,
            [
                'submitText'    => 'Save',
                'cancelText'    => 'Cancel',
                'view'          => 'mothership::theme.resource.single',
                'viewComposer'  => 'Stwt\Mothership\Composer\Resource\Form',
            ]
        );

        $resource = $this->resource->find($id);

        Crumbs::push('active', 'View');

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('show', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = '<iframe style="resize:vertical;" src="'.$resource->url().'" frameborder="0" width="100%" height="600px" />';

        // get the view template and view composer to use
        $view         = Arr::e($config, 'view');
        $viewComposer = Arr::e($config, 'viewComposer');
        
        // Attach a composer to the view
        View::composer($view, $viewComposer);

        return View::make($view, $data);
    }

    public function content($id, $config = [])
    {
        $config['view'] = 'admin.pages.content';
        return parent::edit($id, $config);
    }
}
