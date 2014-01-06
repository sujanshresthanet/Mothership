<?php namespace Stwt\Mothership;

use Config;

class View extends \Illuminate\Support\Facades\View
{
    public static function baseData($data)
    {

        if (isset($data['resource'])) {
            $data = Arr::s($data, 'singular', $data['resource']->singular());
            $data = Arr::s($data, 'plural', $data['resource']->plural());
        }

        return $data;
    }

    public static function makeTemplate($path, $data = [])
    {
        $data = self::baseData($data);
        return parent::make($path, $data);
    }
}
