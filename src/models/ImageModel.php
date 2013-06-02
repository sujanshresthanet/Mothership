<?php namespace Stwt\Mothership;

use HTML;
use Log;
use URL;

class ImageModel extends FileModel
{

    /**
     * Column specification array
     * 
     * @var array
     */
    public $properties = [
        'filename' => [
            'label' => 'Image',
            'type'  => 'file',
        ],
    ];

    /**
     * Allowed mime types
     * 
     * @var array
     */
    public $mimeTypes = ['image/png', 'image/jpeg'];

    /**
     * The route to the img controller that will return our image data
     * 
     * @var string
     */
    protected $route = 'img/{id}/{size}';

    /**
     * The default subdirectory, this is the default dir that the source
     * image will be stored in.
     * 
     * @var string
     */
    protected $defaultSubDirectory = 'source';

    /**
     * Server path to the upload directory root - set it relative to the apps
     * storage directory.
     * 
     * @var string
     */
    protected $path = '/uploads/images';

    /****************************/

    public function src($size = null)
    {
        $size = is_null($size) ? $this->defaultSubDirectory : $size;
        
        $route = $this->route;

        $route = str_replace('{id}', $this->id, $route);
        $route = str_replace('{size}', $size, $route);

        return URL::to($route);
    }

    /**
     * Generate an HTML image element for this resource.
     *
     * @param  string  $size
     * @param  string  $alt
     * @param  array   $attributes
     * @return string
     */
    public function image($size = null, $alt = null, $attributes = array())
    {

        $src = $this->src($size);

        return HTML::image($src, $alt, $attributes);
    }
}
