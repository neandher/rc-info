<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MoneyCustomType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'currency' => 'BRL',
            'grouping' => true,
            'attr' => [
                'onkeydown' => 'MoneyFormat(this,20,event,2);',
                'maxlength' => 12
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return MoneyType::class;
    }
}