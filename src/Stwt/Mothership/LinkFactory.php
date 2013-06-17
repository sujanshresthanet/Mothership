<?php namespace Stwt\Mothership;

use URL;

class LinkFactory
{
    /**
     * The current controller path
     * 
     * @var string
     */
    protected static $controller;

    /**
     * The current controllers path uri
     * 
     * @var string
     */
    protected static $path;

    /**
     * The current controller action
     * 
     * @var string
     */
    protected static $action;

    /**
     * The current resource id
     * 
     * @var int
     */
    protected static $id;

    /**
     * The current related path/id
     * 
     * @var array
     */
    protected static $related;

    public static function seed($config)
    {
        self::$controller = Arr::e($config, 'controller');
        self::$path       = Mothership::pathFromController(self::$controller);
        self::$action     = Arr::e($config, 'action');
        self::$id         = Arr::e($config, 'id');
        self::$related    = Arr::e($config, 'related');
    }

    /**
     * Returns URL to a collection (list) route
     * 
     * @param string $action - the collection action to route to
     *                         defaults to the first action
     * 
     * @return URL
     */
    public static function collection($action)
    {
        $url = 'admin/{related}{controller}';
        
        $data = [
            'controller' => self::$path,
            'related'    => Arr::e(self::$related, 'uri'),
        ];
        return URL::to(self::replace($url, $data));
    }

    /**
     * Returns URL to a single (create) route
     * 
     * @param string $action - the single action to route to
     *                         defaults to the first action
     *                         
     * @return URL
     */
    public static function single($action = null)
    {
        $url = 'admin/{related}{controller}/{action}';
        $action = 'create';
        $data = [
            'controller' => self::$path,
            'action'     => $action,
            'related'    => Arr::e(self::$related, 'uri'),
        ];
        return URL::to(self::replace($url, $data));
    }

    /**
     * Return URL to a resource (edit) route
     * 
     * @param int    $id     - The resource id (will use current id if omitted)
     * @param string $action - The resource action slug, defaults
     *                         to the first action
     * 
     * @return URL
     */
    public static function resource($id = null, $action = 'edit')
    {
        $url = 'admin/{related}{controller}/{id}:{action}';
        $action = 'edit';
        $data = [
            'controller' => self::$path,
            'action'     => $action,
            'id'         => $id ?: self::$id,
            'related'    => Arr::e(self::$related, 'uri'),
        ];
        return URL::to(self::replace($url, $data));
    }

    public static function replace($url, $data = [])
    {
        foreach ($data as $k => $v) {
            $url = str_replace('{'.$k.'}', $v, $url);
        }
        return $url;
    }
}
