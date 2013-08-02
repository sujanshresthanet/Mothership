<?php namespace Stwt\Mothership;

use Content;
use ContentItem;
use Input;
use Log;
use NavigationMenu;
use Redirect;
use URL;
use Page;

class ContentRegionController extends ResourceController
{
    /**
     * The model resource this controller represents
     * 
     * @var string
     */
    public $model = 'ContentRegion';

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
            $getCollection = function ($resource) {
                $page = Page::find($this->related['id']);
                return $page->getAllRegions(false);
            };

            $config['getCollection'] = $getCollection;
            $config['view'] = 'admin.regions.table';
        }

        return parent::table($config);
    }

    public function create($config = [])
    {
        $config['fields'] = [
            'key',
            'type' => [
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
            ],
        ];

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
            ->saveButton(Arr::e($config, 'submitText', 'Save'))
            ->cancelButton(Arr::e($config, 'cancelText', 'Cancel'))
            ->form()
                ->attr('action', '')
                ->generate();

        Crumbs::push('active', 'Edit');

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = $form;

        return View::makeTemplate('mothership::theme.resource.single', $data);
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
        switch ($content->type) {
            case 'html':
                $fields['content']->class = 'input-block-level html';
                $fields['content']->form  = 'textarea';
                $fields['content']->rows  = '30';
                break;
            case 'textarea':
                $fields['content']->class = 'input-block-level';
                $fields['content']->form  = 'textarea';
                $fields['content']->rows  = '30';
                break;
            case 'text':
                $fields['content']->class = 'input-block-level';
                $fields['content']->form  = 'input';
                $fields['content']->type  = 'text';
                $fields['content']->cols  = '10';
                break;
        }

        $form = FormGenerator::resource($content)
            ->method('put')
            ->fields($fields)
            ->saveButton(Arr::e($config, 'submitText', 'Save'))
            ->cancelButton(Arr::e($config, 'cancelText', 'Cancel'))
            ->form()
                ->attr('action', '')
                ->generate();
                
        Crumbs::push('active', 'Edit');

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['content']    = $form;

        return View::makeTemplate('mothership::theme.resource.single', $data);
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
            $contentItem = new ContentItem();
            $contentRegion->contentItems()->save($contentItem);
            Log::error('type: '.Input::get('type'));
            switch (Input::get('type')) {
                case 'nav':
                    $navigationMenu = NavigationMenu::first();
                    $navigationMenu->contentItems()->save($contentItem);
                    break;
                case 'html':
                case 'textarea':
                case 'text':
                    // create new content instance and save to item
                    $content          = new Content;
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
