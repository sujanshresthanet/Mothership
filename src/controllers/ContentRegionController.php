<?php namespace Stwt\Mothership;

use Config;
use Input;
use Log;
use Redirect;
use URL;

class ContentRegionController extends ResourceController
{
    /**
     * The model resource this controller represents
     * 
     * @var string
     */
    public $model = 'Stwt\Mothership\ContentRegionModel';

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
        ],
        'resource' => [
            'edit' => [
                'label' => 'Edit',
                'uri' => '{controller}/{id}:edit',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri' => '{controller}/{id}:delete',
            ],
        ],
        'related' => [
        ],
    ];

    /*
     * When not in a related view, limit the regions to those that have no
     * related page. These are shared regions, that appear on many pages
     *
     * @param object $resource
     *
     * @return object
     */
    protected function queryRelated($resource)
    {
        if ($this->related) {
            return parent::queryRelated($resource);
        } else {
            return $resource->where('page_id', '=', null);
        }
    }


    public function table($config = [])
    {
        $this->before($config);

        if ($this->related) {
            $config['view'] = 'mothership::theme.regions.table';
        }

        return parent::table($config);
    }

    public function getCollection($resource)
    {
        if ($this->related) {
            return parent::getCollection($resource);
            $pageClass = Config::get('mothership::models')['page'];
            $page = $pageClass::find($this->related['id']);
            return $page->getAllRegions(false);
        } else {
            return parent::getCollection($resource);
        }
    }

    public function create($config = [])
    {
        $this->before($config);
        
        $config['fields'] = $this->getRegionFields();

        return parent::create($config);
    }


    /**
     * Custom edit view of region data
     * 
     * Could be a navigation menu (dropdown) or a content area
     * textarea/text input.
     * 
     * @param int $id       - the id of the resource
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function edit($id, $config = [])
    {
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

        switch ($resource->type()) {
            case 'Navigation Menu':
                return $this->editMenu($resource, $config);
                break;
            case 'HTML':
            case 'text':
            case 'textarea':
            default:
                return $this->editContent($resource, $config);
                break;
        }
    }

    protected function getRegionFields()
    {
        $fields = [];

        if ($this->related) {
            $pageClass = Config::get('mothership::models')['page'];
            $page = $pageClass::find($this->related['id']);
            $template = $page->getTemplate();
            $regions = $template->pageRegions();

            $options = [];

            foreach ($regions as $region) {
                $options[ucfirst(humanize($region))] = $region;
            }

            $fields['key'] = [
                'form'    => 'select',
                'label'   => 'Region',
                'name'    => 'key',
                'options' => $options,
                'help' => ['Choose the page region you want to place the new content'],
            ];
        } else {
            $fields[] = 'key';
        }

        $fields['type'] = [
            'form'    => 'select',
            'label'   => 'Type',
            'name'    => 'type',
            'options' => [
                'Navigation Menu' => 'nav',
                'HTML'            => 'html',
                'Plain Text'      => 'textarea',
                'Text'            => 'text',
            ],
            'help' => ['Choose the type of content you wish to add'],
        ];

        return $fields;
    }

    /**
     * Display a dropdown menu to select a NavigationMenu to use in this region
     * 
     * @param  object $resource The ContentRegion object
     * @param  array  $config   Any extra config options
     * 
     * @return View
     */
    protected function editMenu($resource, $config = [])
    {
        $data = [];

        $content = $resource->contentItems()->first();

        $fields = $content->getFields(['content_id']);

        $fields['content_id']->model = $content->content_type;

        $form = FormGenerator::resource($content)
            ->method('put')
            ->fields($fields)
            ->saveButton(Arr::e($config, 'submitText'))
            ->cancelButton(Arr::e($config, 'cancelText'))
            ->form()
                ->attr('action', '')
                ->generate();

        Crumbs::push('active', 'Edit');

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = $form;

        // get the view template and view composer to use
        $view         = Arr::e($config, 'view');
        $viewComposer = Arr::e($config, 'viewComposer');
        
        // Attach a composer to the view
        View::composer($view, $viewComposer);

        return View::make($view, $data);
    }

    /**
     * Display a content editor change the contents in this region.
     * Currently, contents will be one of the following:
     * - HTML
     * - Textarea
     * - Text
     * 
     * @param  object $resource The ContentRegion object
     * @param  array  $config   Any extra config options
     * 
     * @return View
     */
    protected function editContent($resource, $config = [])
    {
        $data = [];

        $content = $resource->contentItems()->first()->content()->first();

        $fields = $content->getFields(['content']);
        $fields = $this->setContentFields($content->type, $fields);
        $form = $this->makeForm($content, $fields, $config);

        Crumbs::push('active', 'Edit');

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = $form->generate();

        // get the view template and view composer to use
        $view         = Arr::e($config, 'view');
        $viewComposer = Arr::e($config, 'viewComposer');
        
        // Attach a composer to the view
        View::composer($view, $viewComposer);

        return View::make($view, $data);
    }

    /**
     * Customise the resources field so it adapts to the type of content we are updateing
     * 
     * @param string $type
     * @param array $fields
     * @param string $fieldName
     *
     * @return array
     */
    protected function setContentFields($type, $fields, $fieldName = 'content')
    {
        switch ($type) {
            case 'html':
                $fields[$fieldName]->class = 'input-block-level html';
                $fields[$fieldName]->form  = 'textarea';
                $fields[$fieldName]->rows  = '30';
                break;
            case 'text':
                $fields[$fieldName]->class = 'input-block-level';
                $fields[$fieldName]->form  = 'input';
                $fields[$fieldName]->type  = 'text';
                $fields[$fieldName]->cols  = '10';
                break;
            case 'textarea':
            default:
                $fields[$fieldName]->class = 'input-block-level';
                $fields[$fieldName]->form  = 'textarea';
                $fields[$fieldName]->rows  = '30';
                break;
        }
        return $fields;
    }

    /**
     * Create a new content region and create the first ContentItem
     * related resource too. Redirect to the content items edit action
     * on succsess
     * 
     * @param array $config - array of optional data
     * 
     * @return Redirect
     */
    public function store($config = [])
    {
        $afterSave = function ($contentRegion) {
            // create new contentItem and save to new region
            $contentItemClass = Config::get('mothership::models')['contentItem'];
            $contentItem = new $contentItemClass;
            $contentRegion->contentItems()->save($contentItem);
            
            switch (Input::get('type')) {
                case 'nav':
                    $navigationMenuClass = Config::get('mothership::models')['navigationMenu'];

                    $navigationMenu = $navigationMenuClass::first();
                    $navigationMenu->contentItems()->save($contentItem);
                    break;
                case 'html':
                case 'textarea':
                case 'text':
                    // create new content instance and save to item
                    $contentClass = Config::get('mothership::models')['content'];

                    $content          = new $contentClass;
                    $content->type    = Input::get('type');
                    $content->content = "Update content";
                    $content->save();

                    $content->contentItems()->save($contentItem);
                    break;
            }
        };

        $config['afterSave'] = $afterSave;

        return parent::store($config);
    }

    /**
     * Custom update action of region data
     * 
     * Could be a navigation menu (dropdown) or a content area
     * textarea/text input.
     * 
     * @param int $id       - the id of the resource
     * @param array $config - array of optional data
     * 
     * @return Redirect
     */
    public function update($id, $config = [])
    {
        $this->before($config);

        $resource = $this->resource->find($id);

        switch ($resource->type()) {
            case 'Navigation Menu':
                return $this->updateMenu($resource, $config);
                break;
            case 'HTML':
            case 'text':
            case 'textarea':
            default:
                return $this->updateContent($resource, $config);
                break;
        }
    }

    /**
     * [update description]
     * 
     * @param object $resource       - the id of the resource
     * @param array  $config - array of optional data
     * 
     * @return Redirect      [description]
     */
    protected function updateMenu($resource, $config = [])
    {
        $contentItem = $resource->contentItems()->first();
        $rules = $contentItem->getRules(['content_id']);

        $contentItem->autoHydrateEntityFromInput    = true;
        $contentItem->autoPurgeRedundantAttributes  = true;
        $contentItem->forceEntityHydrationFromInput = true;    // force hydrate on existing attributes

        if ($contentItem->save($rules)) {
            Messages::add('success', Lang::alert('edit.success', $resource, $this->related));
            return Redirect::to(URL::current());
        } else {
            Messages::add('error', Lang::alert('edit.error', $resource, $this->related));
            return Redirect::to(URL::current())
                ->withInput()
                ->withErrors($resource->errors());
        }
    }


    /**
     * [update description]
     * 
     * @param object $resource       - the id of the resource
     * @param array  $config - array of optional data
     * 
     * @return Redirect           [description]
     */
    protected function updateContent($resource, $config = [])
    {
        $content = $resource->contentItems()->first()->content()->first();
        $rules = $content->getRules(['content']);

        $content->autoHydrateEntityFromInput    = true;
        $content->autoPurgeRedundantAttributes  = true;
        $content->forceEntityHydrationFromInput = true;    // force hydrate on existing attributes

        if ($content->save($rules)) {
            Messages::add('success', Lang::alert('edit.success', $resource, $this->related));
            return Redirect::to(URL::current());
        } else {
            Messages::add('error', Lang::alert('edit.error', $resource, $this->related));
            return Redirect::to(URL::current())
                ->withInput()
                ->withErrors($resource->errors());
        }
    }
}
