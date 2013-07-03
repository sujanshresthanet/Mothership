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

    public function content($id, $config = [])
    {
        $data = [];

        $this->before($config);

        $resource = $this->resource->find($id);

        // get template regions
        $template = $resource->template();

        $regions = $resource->contentRegions()
                            ->with('contentItems')
                            ->get();

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = View::make('mothership::theme.page.content')
                                  ->with('resource', $resource)
                                  ->with('regions', $regions);

        return View::makeTemplate('mothership::theme.resource.single', $data);
    }
}
