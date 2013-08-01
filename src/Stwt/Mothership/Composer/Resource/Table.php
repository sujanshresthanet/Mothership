<?php namespace Stwt\Mothership\Composer\Resource;

use Config;
use \Stwt\Mothership\Arr as Arr;
use \Stwt\Mothership\Composer\Single;
use \Stwt\Mothership\Lang as Lang;

class Table extends Single
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = Lang::title('index', $view->resource);
        }

        if (!isset($view->singular)) {
            $view->singular = 'Singular';
        }

        if (!isset($view->pagination)) {
            $view->pagination = (method_exists($view->collection, 'links') ? $view->collection->links() : '');
        }

        if (!isset($view->primaryColumn)) {
            // Default to the first column in the table
            // as primary column. This data will link to
            // the edit page
            $columns = $view->columns;
            reset($columns);
            $primaryColumn = key($columns);
            $view->primaryColumn = $primaryColumn;
        }

        return $view;
    }
}
