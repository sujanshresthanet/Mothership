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

use Stwt\Mothership\LinkFactory as LinkFactory;

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
    public static $actions = [
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

        $resource = $this->resource;

        $collection = $this->resource->all();

        $data['columns']    = $resource->getColumns(Arr::e($config, 'columns'));
        $data['collection'] = $collection;
        $data['title']      = Lang::title('index', $resource, $this->related);

        return View::make('mothership::theme/resource/index', $data);
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
        $r = Arr::e($config, 'related');
        if ($r) {
            return 'Edit '.$id.' '.$r['path'].' - '.$r['id'];
        } else {
            return 'Edit '.$id;
        }
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
        
    }
}
