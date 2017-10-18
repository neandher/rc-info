<?php

namespace AdminBundle\VichUpload;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class CustomDirNamer implements DirectoryNamerInterface
{
    /**
     * @param mixed $object
     * @param PropertyMapping $mapping
     * @return mixed
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        return date('mY');
    }

}