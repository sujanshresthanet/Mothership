<?php namespace Stwt\Mothership\Composer\Resource;

use Config;
use \Stwt\Mothership\Arr as Arr;
use \Stwt\Mothership\Composer\Sidebar;
use \Stwt\Mothership\Crumbs as Crumbs;

class Single extends Single
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = 'Table View';
        }

        if (!isset($view->singular)) {
            $view->singular = 'Singular';
        }
        
        if (!isset($view->breadcrumbs)) {
            $view->breadcrumbs = Crumbs::generate();
        }

        return $view;
    }
}
