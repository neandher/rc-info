<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MtCheckboxType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'mt_checkbox';
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return CheckboxType::class;
    }


}