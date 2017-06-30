<?php

namespace SiteBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class CustomerAware
 * @package SiteBundle\Annotation
 *
 * @Annotation
 * @Target("CLASS")
 */
final class CustomerAware
{
    public $customerFieldName;
}