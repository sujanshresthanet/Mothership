<?php
/**
 * MothershipResourceController.php
 *
 * PHP version 5.4.x
 *
 * @category MothershipResourceController
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */

namespace Stwt\Mothership;

use Log;
use Request;
use Stwt\Mothership\LinkFactory as LinkFactory;
use URL;

/**
 * MothershipResourceController
 *
 * The resource controller handles all the common CRUD actions in the mothership.
 *
 * @category Controller
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */
class ResourceController extends BaseController
{
    public $model;

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
                'label' => 'Add',
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
    
    protected $resource;
    protected $related = null;

    public function __construct()
    {
        parent::__construct();

        $this->resource = new $this->model;
    }

    protected function before($config = [])
    {
        LinkFactory::seed($config);

        $this->related = Arr::e($config, 'related');
    }

    /**
     * Retuns a table of resources
     * 
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function index($config = [])
    {
        $data = [];

        $this->before($config);

        $resource   = $this->resource;
        $collection = $this->queryRelated($resource);
        $collection = $this->queryOrderBy($collection);
        $collection = $collection->paginate(15);

        $data['columns']    = $resource->getColumns(Arr::e($config, 'columns'));
        $data['collection'] = $collection;
        $data['title']      = Lang::title('index', $resource, $this->related);

        return View::make('mothership::theme.resource.index', $data);
    }

    /**
     * Retuns a form to create a new resource
     * 
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function create($config = [])
    {
        $r = Arr::e($config, 'related');
        if ($r) {
            return 'Create '.$r['path'].' - '.$r['id'];
        } else {
            return 'Create';
        }
    }

    /**
     * Displays an existing resource
     * 
     * @param int $id       - the id of the resource
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function show($id, $config = [])
    {
        $r = Arr::e($config, 'related');
        if ($r) {
            return 'Show '.$id.' '.$r['path'].' - '.$r['id'];
        } else {
            return 'Show '.$id;
        }
    }

    /**
     * Displays a form to update an existing resource
     * 
     * @param int $id       - the id of the resource
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function edit($id, $config = [])
    {
        $data = [];

        $this->before($config);

        $resource = $this->resource->find($id);

        $fields = $resource->getFields(Arr::e($config, 'fields'));

        $form = FormGenerator::resource($resource)
            ->method('put')
            ->fields($fields)
            ->saveButton(Arr::e($config, 'submitText', 'Save'))
            ->cancelButton(Arr::e($config, 'cancelText', 'Cancel'))
            ->form()
                ->attr('action', '')
                ->generate();

        $data['tabs']       = $this->getTabs($resource);
        $data['title']      = Lang::title('edit', $resource, $this->related);
        $data['resource']   = $resource;
        $data['form']       = $form;

        return View::make('mothership::theme.resource.form', $data);
    }

    /**
     * Displays a conformation form to delete an existing resource
     * 
     * @param int $id       - the id of the resource
     * @param array $config - array of optional data
     * 
     * @return View
     */
    public function delete($id, $config = [])
    {
        $r = Arr::e($config, 'related');
        if ($r) {
            return 'Delete '.$id.' '.$r['path'].' - '.$r['id'];
        } else {
            return 'Delete '.$id;
        }
    }

    public function store($config = [])
    {
        return 'Store';
    }

    public function update($id, $config = [])
    {
        return 'Update';
    }

    public function destroy($id, $config = [])
    {
        return 'Destroy';
    }

    ##########################################################
    # MOVE Query Logic INTO SEPARATE CLASS!
    ##########################################################

    /*
     * Checks if the current request is for related resources and
     * adds that relationship clause to the query
     *
     * @param object $resource
     *
     * @return object
     */
    protected function queryRelated($resource)
    {
        if ($this->related) {
            Log::error('Yes : is related request');

            $resource = $this->related['resource'];
            $id       = $this->related['id'];
            $hasMany  = $this->resource->hasManyName();
            
            return $resource->$hasMany();
        }
        return $resource;
    }

    /*
     * Adds an order by clause to the resource query
     *
     * @param object $resource
     *
     * @return object
     */
    protected function queryOrderBy($resource)
    {
        return $resource->orderBy('created_at', 'desc');
    }

    ##########################################################
    # MOVE Tab Construction INTO SEPARATE CLASS!
    ##########################################################

    /**
     * Prepares the action navigation tabs for view rendering
     * We replace placeholder strings like {controller} & {id}
     * with their proper values
     *
     * @return array
     */
    protected function getTabs($resource)
    {
        $array = [];
        $actions = $this->getActions(['resource', 'related', 'single']);
        foreach ($actions as $key => $action) {
            $route = $action['uri'];
            // replace {controller} with current controller uri segment
            $uri = str_replace('{controller}', Request::segment(2), $route);

            if ($resource->id) {
                // replace {id} with current resource id
                $uri = str_replace('{id}', $resource->id, $uri);
            } else {
                // if this action has no current resource disable the action
                $uri = (strpos($uri, '{id}') === false ? $uri : null);
            }

            if ($uri) {
                $uri = 'admin/'.$uri;
            }

            // add classes
            if (!isset($action['class'])) {
                $action['class'] = [];
            }
            if (Request::is($uri)) {
                $action['class'][] = 'active';
            }
            if (!$uri) {
                $action['class'][] = 'disabled';
            }

            $action['class'] = implode(' ', $action['class']);

            if ($uri) {
                $action['link'] = '<a href="'.URL::to($uri).'">'.$action['label'].'</a>';
            } else {
                $action['link'] = '<a>'.$action['label'].'</a>';
            }

            $array[$route] = $action;
        }
        return $array;
    }

    /**
     * Returns array of actions specified in this controller.
     * $type can either be a action group (string) or multiple
     * groups (array). Common action groups are as follows:
     * - collections
     * - single
     * - resource
     * - related
     *
     * @param string/array $type
     * @return array
     */
    protected function getActions($type)
    {
        if (is_string($type)) {
            return (isset($this->actions[$type]) ? $this->actions[$type] : []);
        } else {
            $actions = [];
            foreach ($type as $t) {
                $actions = array_merge($actions, $this->getActions($t));
            }
            return $actions;
        }
    }

    /**
     * Return an action array by key and group. If either
     * the key or group does not exist, an empty array is returned
     * i.e. getAction('view', 'resource');
     *
     * @param string $key
     * @param string $group
     *
     * @return array
     */
    public function getAction($key, $group)
    {
        if (isset($this->actions[$group]) && isset($this->actions[$group][$key])) {
            return $this->actions[$group][$key];
        }
        return [];
    }

    /*
     * Returns the full url to a resource action.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getResourceUrl($uri)
    {
        return URL::to($this->getResourceUri($uri));
    }

    /*
     * Returns the uri to a resource action.
     *
     * Takes a simple uri like controller/{id}/edit.
     * Suffixes the admin base uri and prefixes any related
     * uri information.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function getResourceUri($uri)
    {
        $uri = 'admin/'.$uri;
        if ($this->related) {
            foreach ($this->related as $relatedModel => $relatedId) {
                $uri .= '/'.$relatedModel.'/'.$relatedId;
            }
        }
        return $uri;
    }
}
