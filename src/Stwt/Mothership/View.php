<?php namespace Stwt\Mothership;

use Config;

class View extends \Illuminate\Support\Facades\View
{
    public static function baseData($data)
    {
        $data = Arr::s($data, 'navigation', Config::get('mothership::primaryNavigation'));

        return $data;
    }

    public static function make($path, $data = [])
    {
        $data = self::baseData($data);
        return parent::make($path, $data);
    }
}
