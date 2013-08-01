<?php namespace Stwt\Mothership\Composer\Resource;

use Config;

use \Stwt\Mothership\Arr as Arr;

class Base
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = 'Resource View';
        }

        if (!isset($view->navigation)) {
            $view->navigation = Config::get('mothership::primaryNavigation');
        }

        return $view;
    }
}
