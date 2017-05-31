<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Validator\Constraints\CustomerUserPassword;
use UserBundle\Form\Type\ChangePasswordType;
use UserBundle\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerChangePasswordType extends ChangePasswordType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('current_password');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => UserInterface::class,
                'validation_groups' => ['Default', 'changing'],
                'is_register' => false
            )
        );
    }
}
