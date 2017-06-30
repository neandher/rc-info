<?php

namespace SiteBundle\Doctrine;

use SiteBundle\Annotation\CustomerAware;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class CustomerFilter extends SQLFilter
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @inheritDoc
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader)) {
            return '';
        }

        /** @var CustomerAware $customerAware */
        $customerAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            CustomerAware::class
        );

        if (!$customerAware) {
            return '';
        }

        $fieldName = $customerAware->customerFieldName;

        try {
            $customerId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        if (empty($fieldName) || empty($customerId)) {
            return '';
        }

        $query = sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $customerId);

        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }

}