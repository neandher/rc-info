<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SwitchType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'switch';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return CheckboxType::class;
    }

}