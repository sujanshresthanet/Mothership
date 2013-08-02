<?php namespace Stwt\Mothership\Composer\Resource;

use Config;
use \Stwt\Mothership\Arr as Arr;
use \Stwt\Mothership\Composer\Single;
use \Stwt\Mothership\Lang as Lang;

class Form extends Single
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = Lang::title('index', $view->resource);
        }

        if (!isset($view->singular)) {
            $view->singular = $view->resource->singular();
        }

        if (!isset($view->plural)) {
            $view->plural = $view->resource->plural();
        }

        return $view;
    }
}
