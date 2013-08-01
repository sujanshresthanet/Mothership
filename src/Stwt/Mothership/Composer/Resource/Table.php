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

        if (!isset($view->primaryColumn)) {
            $view->primaryColumn = 'Name';
        }

        return $view;
    }
}
