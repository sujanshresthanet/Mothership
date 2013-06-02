<?php namespace Stwt\Mothership;

use Hash;
use Messages;
use Redirect;
use Request;

class MothershipImageController extends FileController
{
    public function store($relatedModel = null, $relatedId = null)
    {
        if ($relatedModel && $relatedId) {
            $this->related[$relatedModel] = $relatedId;
            $relatedResource = $relatedModel::find($relatedId);
        }
        $controller = Request::segment(2);
        $path       = $this->resource->getPath();
        $storage    = new \Upload\Storage\FileSystem($path);
        $file       = new \Upload\File('filename', $storage);
        $mimeTypes  = ['image/png', 'image/jpeg'];

        $rules = [
            new \Upload\Validation\Mimetype($mimeTypes),
            new \Upload\Validation\Size('20M'),
        ];

        // Validate file upload
        $file->addValidations($rules);

        // Try to upload file
        try {
            // Success!
            $file->upload();
        } catch (\Exception $e) {
            // Fail!
            $errors = $file->getErrors();
            return Redirect::to('admin/'.$controller.'/create');
        }

        $this->renameUploadedFile($file);

        if (!$this->resource->save()) {
            Messages::add('error', 'Error saving record.');
            $uri = $this->getResourceUri($controller.'/create');
            Redirect::to($uri);
        } else {
            if (isset($relatedResource)) {
                $plural = $this->resource->plural(false);
                if ($relatedResource->$plural()->save($this->resource)) {
                    Messages::add('success', 'New related record created.');

                    $uri = $this->getResourceUri($controller.'/index');
                    return Redirect::to($uri);
                } else {
                    Messages::add('error', 'Error creating related record');
                    $uri = $this->getResourceUri($controller.'/create');
                    Redirect::to($uri);
                }
            } else {
                $uri = $this->getResourceUri($controller);
                Messages::add('success', 'New record created.');
                return Redirect::to($uri);
            }
        }

        return Redirect::to('admin/'.$controller);
    }

    /*
     * Generates a random filename for the newly uploaded
     * file. Renames the file and saves both the old and 
     * new names to the resource.
     *
     * @param object $file
     *
     * @return boolean
     */
    protected function renameUploadedFile($file)
    {
        // Try to rename the image
        $oldName = $file->getName();
        $newName = static::randomString(20);
        $ext     = $file->getExtension();
        $path    = $this->resource->getPath();

        $this->resource->title      = $oldName;
        $this->resource->filename   = $newName;
        $this->resource->extension  = $ext;
        //$this->resource->mime_type  = $file->getMimetype();

        return rename($path.'/'.$oldName.'.'.$ext, $path.'/'.$newName.'.'.$ext);
    }

    /*
     * Generate a random alpha numeric string
     *
     * @param $length specify the lenght of the returned string
     *
     * @return string
     */
    protected function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}