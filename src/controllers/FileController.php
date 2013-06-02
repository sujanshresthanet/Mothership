<?php namespace Stwt\Mothership;

use GoodForm;
use Log;
use Input;
use Request;
use Redirect;
use Session;
use URL;
use View;

class FileController extends ResourceController
{
    /**
     * Default Actions in this controller
     */
    public $actions = [
        'collection' => [
            'index' => [
                'label' => 'All',
                'uri' => '{controller}/index',
            ],
        ],
        'single' => [
            'create' => [
                'label' => 'Upload',
                'uri' => '{controller}/create',
            ],
        ],
        'resource' => [
            'view' => [
                'label' => 'View',
                'uri' => '{controller}/{id}',
            ],
            'edit' => [
                'label' => 'Edit',
                'uri' => '{controller}/{id}/edit',
            ],
            'delete'  => [
                'label' => 'Delete',
                'uri' => '{controller}/{id}/delete',
            ],
        ],
        'related' => [
        ],
    ];

    protected $errorMessages = [];

    /**
     * Define the upload action
     * 
     * @param  array $config
     * @return view
     */
    public function create($config = [])
    {
        $config = Arr::s($config, 'breadcrumb', 'Upload');
        $config = Arr::s($config, 'fields', ['filename']);
        return parent::create($config);
    }

    /**
     * Extend the store method to upload the posted file first and then save.
     * 
     * @param  array $config
     * 
     * @return Redirect
     */
    public function store($config = [])
    {
        $resource = $this->resource;

        $file = $this->uploadFile($resource);
        
        if ($this->errorMessages) {
            $redirectUrl = URL::to('admin/'.$this->controller.'/create');
            $message = $this->getAlert($resource, 'error', $config, 'create');
            Messages::add('error', $message);
            return Redirect::to($redirectUrl)
                ->withErrors($this->errorMessages);
        }

        $resource->renameFile($file);

        $resource->save();

        $message = $this->getAlert($resource, 'success', $config, 'create');
        Messages::add('success', $message);

        $redirectUrl = URL::to('admin/'.$this->controller);
        return Redirect::to($redirectUrl);
    }

    /**
     * Extend the destroy method to delete a file from the server
     * as well as the database
     * 
     * @param int $id
     * @param array $config
     * 
     * @return Redirect
     */
    public function destroy($id, $config = [])
    {
        $config = Arr::s(
            $config,
            'beforeDelete',
            function ($file) {
                $file->deleteFile();
            }
        );

        return parent::destroy($id, $config);
    }

    /**
     * Extend the destry collection method to delete each file in
     * the collection from the server as well as the database.
     * 
     * @param  array $config
     * 
     * @return Redirect
     */
    public function destroyCollection($config = [])
    {
        $config = Arr::s(
            $config,
            'beforeDelete',
            function ($collection) {
                foreach ($collection as $file) {
                    $file->deleteFile();
                }
            }
        );

        return parent::destroyCollection($config);
    }

    /**********************************/

    /**
     * Attempt to upload a new file.
     * Check:
     * - That a file was posted
     * - The directory exists & is writable
     * - The file passes size and mimetype validation
     *
     * @param  object $resource
     * @return \Upload\File
     */
    protected function uploadFile($resource)
    {
        if (!Input::hasFile('filename')) {
            $this->errorMessages = ['filename' => 'Please choose a file to upload'];
            return;
        }

        $storage = $this->prepareStorage($resource);
        if (!$storage) {
            return null;
        }

        try {
            $file = new \Upload\File('filename', $storage);
            $mimeTypes = $resource->mimeTypes;
            $maxSize   = $resource->maxSize;
            $rules = [
                new \Upload\Validation\Mimetype($mimeTypes),
                new \Upload\Validation\Size($maxSize),
            ];
            $file->addValidations($rules);
            $file->upload();
            return $file;
        } catch (\Exception $e) {
            $errors = $file->getErrors();
            $errorString = implode(', ', $errors);
            $this->errorMessages = [
                'filename' => $errorString,
            ];
            return null;
        }
    }

    /**
     * Prepare the storage object, catch any exceptions if
     * there are issues with the directory
     * @param  object $resource
     * @return \Upload\Storage\FileSystem
     */
    protected function prepareStorage($resource)
    {
        $path = $resource->getPath();
        try {
            $storage = new \Upload\Storage\FileSystem($path);
            return $storage;
        } catch (\Exception $e) {
            $this->errorMessages = ['filename' => $e->getMessage()];
            return null;
        }
    }
}
