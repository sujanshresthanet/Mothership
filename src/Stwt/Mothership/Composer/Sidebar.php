<?php namespace Stwt\Mothership\Composer;

use Config;
use \Stwt\Mothership\Arr as Arr;

class Sidebar extends Base
{
    public function compose($view)
    {
        $view = parent::compose($view);

        $view['title']    = 'Sidebar View';

        return $view;
    }
}
