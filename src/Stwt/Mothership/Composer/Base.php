<?php namespace Stwt\Mothership\Composer;

use Config;

use \Stwt\Mothership\Arr as Arr;

class Base
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = 'Base View';
        }

        if (!isset($view->navigation)) {
            $view->navigation = Config::get('mothership::primaryNavigation');
        }

        if (!isset($view->appTitle)) {
            $view->appTitle = Config::get('mothership::appTitle');
        }

        if (!isset($view->appStyle)) {
            $view->appStyle = Config::get('mothership::appStyle');
        }

        if (!isset($view->appScript)) {
            $view->appScript = Config::get('mothership::appScript');
        }

        return $view;
    }
}
