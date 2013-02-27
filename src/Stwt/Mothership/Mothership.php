<?php namespace Stwt\Mothership;

use URL;

class Mothership {

    /**
     * Create a bootsrap button with icon
     *
     * @param   string  $uri
     * @param   string  $text
     * @param   string  $text
     * @return  string
     */
    public static function button($uri, $text, $type='create') 
    {
        $classes = 'btn pull-right';
        $url     = URL::to($uri);
        switch ($type)
        {
            case 'create':
                $icon       = '<i class="icon-white icon-plus"></i> ';
                $classes   .= ' btn-success';
                break;
        }
        return '<a class="'.$classes.'" href="'.$url.'">'.$icon.$text.'</a>';
    }
}