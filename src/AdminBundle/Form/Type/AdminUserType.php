<?php

namespace AdminBundle\Form\Type;

use AdminBundle\Entity\AdminUser;
use UserBundle\Form\Type\UserType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends UserType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName', TextType::class, ['label' => 'Primeiro Nome'])
            ->add('lastName', TextType::class, ['label' => 'Segundo Nome'])
            ->remove('isEnabled')
            ->add('isEnabled', SwitchType::class, ['label' => 'user.form.enabled'])
            ->add('isSuperAdmin', SwitchType::class, [
                'label' => 'user.form.is_super_admin',
                'mapped' => false,
                'required' => false,
                'data' => $options['data']->isSuperAdmin()
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdminUser::class,
            'validation_groups' => ['Default', 'creating'],
            'is_edit' => false,
        ]);
    }

}