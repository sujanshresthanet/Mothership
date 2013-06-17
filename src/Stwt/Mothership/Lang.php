<?php namespace Stwt\Mothership;

class Lang extends \Illuminate\Support\Facades\Lang
{
    /**
     * Return the page title for a view from a language file
     * 
     * @param string $category - The language string category
     * @param string $view     - The view name
     * @param object $resource - The resource instance
     * @param array  $related  - A related resource [optional]
     * 
     * @return string
     */
    public static function get($category, $view, $resource, $related = null)
    {
        $view = $related ? 'r'.$view : $view;
        $key = 'mothership::mothership.'.$category.'.'.$view;

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
        return self::get('title', $view, $resource, $related);
    }

    /**
     * Return the table/list caption for a view from a language file
     * 
     * @param [type] $view     - The view name
     * @param object $resource - The resource instance
     * @param array  $related  - A related resource [optional]
     * 
     * @return string
     */
    public static function caption($view, $resource, $related = null)
    {
        
        return self::get('caption', $view, $resource, $related);
    }

    /**
     * Return the alert message for a view from a language file
     * 
     * @param [type] $key      - The alert name e.g. create.success, update.error
     * @param object $resource - The resource instance
     * @param array  $related  - A related resource [optional]
     * 
     * @return string
     */
    public static function alert($key, $resource, $related = null)
    {
        
        return self::get('alert', $key, $resource, $related);
    }
}
