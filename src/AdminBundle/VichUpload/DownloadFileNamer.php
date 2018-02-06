<?php

namespace AdminBundle\VichUpload;

use AdminBundle\Entity\Downloads;
use Gedmo\Sluggable\Util\Urlizer;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class DownloadFileNamer implements NamerInterface
{
    /**
     * @param Downloads $object
     * @param PropertyMapping $mapping
     * @return mixed
     */
    public function name($object, PropertyMapping $mapping)
    {
        $str = str_replace(
            "." . $mapping->getFile($object)->getClientOriginalExtension(),
            "",
            $mapping->getFile($object)->getClientOriginalName()
        );
        return date('mY') . '/' . Urlizer::urlize($str, '_') . '.' . $mapping->getFile($object)->getClientOriginalExtension();
    }

}