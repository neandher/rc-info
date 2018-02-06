<?php

namespace AdminBundle\VichUpload;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;

class CustomFileNamer implements NamerInterface
{
    use FileExtensionTrait;

    /**
     * @param mixed $object
     * @param PropertyMapping $mapping
     * @return mixed
     */
    public function name($object, PropertyMapping $mapping)
    {
        $file = $mapping->getFile($object);
        $name = str_replace('.', '', uniqid('', true));

        if ($extension = $this->getExtension($file)) {
            $name = sprintf('%s.%s', $name, $extension);
        }

        return date('mY') . '/' . $name;
    }

}