<?php namespace Stwt\Mothership;

use HTML;
use Log;
use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;
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
     * Array of image dimensions this image is available in
     * 
     * @var array
     */
    public $sizes = [
        'source' => [
            'w' => null,
            'h' => null,
            'p' => 'http://placehold.it/260x180',
        ],
        'thumb' => [
            'w' => 100,
            'h' => null,
            'p' => 'http://placehold.it/100x100',
        ],
    ];

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
     * The subdirectory thumbnails are stored in
     * 
     * @var string
     */
    protected $thumbnailSubDirectory = 'thumb';

    /**
     * Server path to the upload directory root - set it relative to the apps
     * storage directory.
     * 
     * @var string
     */
    protected $path = '/uploads/images';

    /****************************/

    /**
     * Delete images from the server
     * 
     * @param string $subDirectory
     * 
     * @return [type]
     */
    public function deleteFile($subDirectory = null)
    {
        if (!$subDirectory) {
            foreach ($this->sizes as $name => $dimensions) {
                parent::deleteFile($name);
            }
            return true;
        } else {
            parent::deleteFile($subDirectory);
        }
    }

    /**
     * Returns the public url to the image. If this instance is empty
     * we return the placehoder image specified for the image size
     * 
     * @param  string $size
     * @return string
     */
    public function src($size = null)
    {
        $size = is_null($size) ? $this->defaultSubDirectory : $size;
        Log::error('get src '.$size);
        if ($this->id) {
            $route = $this->route;

            $route = str_replace('{id}', $this->id, $route);
            $route = str_replace('{size}', $size, $route);

            return URL::to($route);
        } else {
            return $this->sizes[$size]['p'];
        }
    }

    /**
     * Generate an HTML image element for this resources thumbnail image.
     *
     * @param  string  $alt
     * @param  array   $attributes
     * @return string
     */
    public function thumbnail($alt = null, $attributes = array())
    {
        return $this->image($this->thumbnailSubDirectory, $alt, $attributes);
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

        $alt = $alt ?: (string)$this;

        return HTML::image($src, $alt, $attributes);
    }

    /**
     * Initialise a new uploaded image by rezising to all predifined
     * sizes
     * 
     * @return void
     */
    public function initImage()
    {
        foreach ($this->sizes as $name => $dimensions) {
            if ($this->defaultSubDirectory == $name) {
                continue;
            }
            $this->resizeImage($name, $dimensions);
        }
    }

    /**
     * Resize the image to a predefined size
     * 
     * @param  string $size
     * @param  array $dimensions
     * 
     * @return void
     */
    protected function resizeImage($name, $dimensions)
    {
        // load image from master subdir
        $layer = ImageWorkshop::initFromPath($this->getFilePath());

        // resize specs
        $thumbWidth         = $dimensions['w']; // px
        $thumbHeight        = $dimensions['h'];
        $conserveProportion = true;
        $positionX          = 0; // px
        $positionY          = 0; // px
        $position           = 'MM';
         
        $layer->resizeInPixel(
            $thumbWidth,
            $thumbHeight,
            $conserveProportion,
            $positionX,
            $positionY,
            $position
        );

        // Saving the result
        $dirPath         = $this->getPath($name);
        $filename        = $this->getFilename();
        $createFolders   = true;
        $backgroundColor = null;
        $imageQuality    = 95;
        
        Log::error('save '.$dirPath);

        $layer->save(
            $dirPath,
            $filename,
            $createFolders,
            $backgroundColor,
            $imageQuality
        );
    }
}
