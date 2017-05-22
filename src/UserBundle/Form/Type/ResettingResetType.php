<?php

namespace UserBundle\Form\Type;

use UserBundle\Model\UserInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingResetType extends PlainPasswordType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => UserInterface::class,
                'validation_groups' => ['Default', 'resetting'],
                'is_register' => false
            )
        );
    }
}