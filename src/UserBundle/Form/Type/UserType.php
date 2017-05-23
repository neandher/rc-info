<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use UserBundle\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends PlainPasswordType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'E-mail'])
            ->add('isEnabled', ChoiceType::class, [
                'required' => false,
                'label' => 'user.form.is_enabled',
                'choices' => [
                    'user.form.is_enabled' => 1,
                    'user.form.is_not_enabled' => 0
                ]
            ]);

        if (!$options['is_edit']) {
            parent::buildForm($builder,$options);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UserInterface::class,
                'is_edit'    => false,
            ]
        );
    }

}