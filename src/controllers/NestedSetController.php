<?php
/**
 * NestedSetController.php
 *
 * PHP version 5.4.x
 *
 * @category NestedSetController
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */

namespace Stwt\Mothership;

use Input;
use Log;
use Redirect;
use Request;
use Stwt\GoodForm\GoodForm as GoodForm;
use Stwt\Mothership\LinkFactory as LinkFactory;
use URL;
use Validator;

/**
 * NestedSetController
 *
 * The nested set controller adds ordering actions
 *
 * @category Controller
 * @package  Mothership
 * @author   Jim Wardlaw <jim@stwt.co>
 * @license  http://www.wtfpl.net/txt/copying/ DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
 * @link     http://stwt.co/
 */
class NestedSetController extends ResourceController
{
    public function order($config = [])
    {
        $data = [];

        $this->before($config);

        $resource   = $this->resource;
        $collection = $this->queryRelated($resource);
        $collection = $this->queryOrderBy($collection);
        $collection = $collection->paginate(15);

        $data['caption']    = Lang::caption('index', $resource, $this->related);
        $data['columns']    = $resource->getColumns(Arr::e($config, 'columns'));
        $data['collection'] = $collection;
        $data['resource']   = $resource;
        $data['title']      = Lang::title('index', $resource, $this->related);

        return View::make('mothership::theme.resource.order', $data);
    }
}
