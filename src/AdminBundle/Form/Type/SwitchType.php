<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SwitchType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'data-on-color' => 'success',
                'data-off-color' => 'danger',
                'data-on-text' => 'Sim',
                'data-off-text' => 'Não',
            ]
        ]);
    }

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