<?php namespace Stwt\Mothership;

class Crumbs
{
    protected static $breadcrumbs = [];

    public static function push($uri, $label)
    {
        static::$breadcrumbs[$uri] = $label;
    }

    public static function generate()
    {
        return View::make('mothership::theme.common.breadcrumbs')
                   ->with('breadcrumbs', static::$breadcrumbs)
                   ->render();
    }
}
