<?php namespace Stwt\Mothership;

class Lang extends \Illuminate\Support\Facades\Lang
{
    /**
     * Return the page title for a view from a language file
     * 
     * @param [type] $view     - The view name
     * @param object $resource - The resource instance
     * @param array  $related  - A related resource [optional]
     * 
     * @return string
     */
    public static function title($view, $resource, $related = null)
    {
        $view = $related ? 'r'.$view : $view;
        $key = 'mothership::mothership.titles.'.$view;

        $placeHolders = [
            'singular'  => $resource->singular(),
            'plural'    => $resource->plural(),
            'resource'  => $resource->__toString(),
        ];

        if ($related) {
            $relatedResource = $related['resource'];
            $placeHolders['rsingular'] = $relatedResource->singular();
            $placeHolders['rplural']   = $relatedResource->plural();
            $placeHolders['rresource'] = $relatedResource->__toString();
        }

        return parent::get($key, $placeHolders);
    }
}
