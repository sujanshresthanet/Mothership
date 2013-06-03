<?php namespace Stwt\Mothership;

use Str;
use Log;
use Illuminate\Support\Facades\File as File;

class FileModel extends BaseModel
{
    /**
     * Column specification array
     * 
     * @var array
     */
    public $properties = [
        'filename' => [
            'label' => 'File',
            'type'  => 'file',
        ],
    ];

    protected $hidden       = ['created_at', 'updated_at'];
    protected $guarded      = ['id', 'filename', 'mime_type', 'created_at', 'updated_at'];

    /**
     * Allowed mime types
     * 
     * @var array
     */
    public $mimeTypes = [];

    /**
     * The maximum size for an uploaded file
     * 
     * @var string
     */
    public $maxSize = '20M';

    /**
     * The default subdirectory, this is the default dir that the source
     * image will be stored in. null = no subdir
     * 
     * @var string
     */
    protected $defaultSubDirectory = null;

    /**
     * Server path to the upload directory root - set it relative to the apps
     * storage directory.
     * 
     * @var string
     */
    protected $path = '/uploads/files';

    /**
     * Constructs the instance and set's our upload dir
     */
    public function __construct ()
    {
        parent::__construct();
        $this->path = storage_path().$this->path;
    }

    public function __toString()
    {
        if ($this->title) {
            return $this->title;
        }
        return parent::__toString();
    }

    /**
     * Rename the file to a radom string and retain the original name in the
     * title property. Also assign any other data to the model from the file
     * like extension and mime type.
     *
     * @note Issue with getMimetype(). Requires the 'fileinfo' extension.
     * 
     * @param \Upload\File $file
     * 
     * @return boolean
     */
    public function renameFile($file)
    {
        // old path
        $oldFilename = $this->getPath().'/'.$file->getNameWithExtension();

        $this->title      = $file->getName();
        $this->filename   = Str::random();
        $this->extension  = $file->getExtension();
        $this->mime_type  = '';
        //$file->getMimetype();

        // new path
        $newFilename = $this->getFilePath();

        return rename($oldFilename, $newFilename);
    }

    /**
     * Delete the file from the server
     * 
     * @param string $subDirectory
     * 
     * @return [type]
     */
    public function deleteFile($subDirectory = null)
    {
        if ($this->fileExists($subDirectory)) {
            return unlink($this->getFilePath($subDirectory));
        }
        return true;
    }

    /**
     * Check file exists on the server
     *
     * @param string $subDirectory
     * 
     * @return boolean
     */
    public function fileExists($subDirectory = null)
    {
        return file_exists($this->getFilePath($subDirectory));
    }

    /**
     * Return the path to the directory this file is stored in
     * 
     * @param string $subDirectory
     * 
     * @return string
     */
    public function getPath ($subDirectory = null)
    {
        $subDirectory = ($subDirectory ? $subDirectory : $this->defaultSubDirectory);
        return $subDirectory ? $this->path.'/'.$subDirectory : $this->path;
    }

    /**
     * Return the full path with filename of the file on the server
     * 
     * @param string $subDirectory
     * 
     * @return string
     */
    public function getFilePath ($subDirectory = null)
    {
        return $this->getPath($subDirectory).'/'.$this->getFilename();
    }

    /**
     * Return the full filename of the file on the server
     * 
     * @return string
     */
    public function getFilename()
    {
        return $this->filename.'.'.$this->extension;
    }

    /**
     * Return an instance of the File object for this file
     * 
     * @return [type]
     */
    public function getFile($subDirectory = null)
    {
        return File::get($this->getFilePath($subDirectory));
    }
}
