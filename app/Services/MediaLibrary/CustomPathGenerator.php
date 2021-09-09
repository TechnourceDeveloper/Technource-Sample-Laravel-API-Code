<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $model = $media->getCustomProperty('folder_name');
        $directoryPath =public_path().'/storage/'.$model;
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            chmod($directoryPath, 0777);
        }
        else{
            chmod($directoryPath, 0777);
        }
        return (!empty($model))?$model:'' ;

//        return $currentTenant->unique_id.DIRECTORY_SEPARATOR.$media->getKey();
    }


    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        $directoryPath =public_path().'/storage/'.$this->getBasePath($media). '/responsive-images/';
        if (! file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            chmod($directoryPath, 0777);
        }

        return $this->getBasePath($media) . '/responsive-images/';
    }


}


