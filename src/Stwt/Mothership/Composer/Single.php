<?php namespace Stwt\Mothership\Composer;

use Config;
use \Stwt\Mothership\Arr as Arr;
use \Stwt\Mothership\Crumbs as Crumbs;

class Single
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = 'Single View';
        }

        if (!isset($view->breadcrumbs)) {
            $view->breadcrumbs = Crumbs::generate();
        }

        return $view;
    }
}
