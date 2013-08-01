<?php namespace Stwt\Mothership\Composer;

use Config;
use \Stwt\Mothership\Arr as Arr;

class Single
{
    public function compose($view)
    {
        if (!isset($view->title)) {
            $view->title = 'Single View';
        }

        return $view;
    }
}
