<?php namespace Stwt\Mothership;

use URL;
use Log;

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
     * The type of action in use
     * 
     * @var string
     */
    protected static $type;

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
        self::$type       = Arr::e($config, 'type');
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
    public static function collection($action = null)
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

    public static function isRelated()
    {
        return (self::$related);
    }

    public static function isCollection()
    {
        return (self::getType() == 'collection');
    }

    public static function isResource()
    {
        return (self::getType() == 'resource');
    }

    public static function getController()
    {
        return self::$controller;
    }

    public static function getPath()
    {
        return self::$path;
    }

    public static function getAction()
    {
        return self::$action;
    }

    public static function getId()
    {
        return self::$id;
    }

    public static function getType()
    {
        return self::$type;
    }

    public static function getModel()
    {
        return Mothership::modelFromPath(self::$path);
    }

    public static function getResource()
    {
        $path = self::$path;
        $id = self::$id;

        return Mothership::resourceFromPath($path, $id);
    }

    public static function getRelated()
    {
        return self::$related;
    }

    public static function getRelatedPath()
    {
        return self::$related['path'];
    }

    public static function getRelatedId()
    {
        return self::$related['id'];
    }

    public static function getRelatedResource()
    {
        return self::$related['resource'];
    }

    public static function getRelatedUri()
    {
        return self::$related['uri'];
    }
}
