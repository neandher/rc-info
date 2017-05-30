<?php

namespace AdminBundle\Form\Type;

use SiteBundle\Entity\SiteUser;
use UserBundle\Form\Type\PlainPasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteUserType extends PlainPasswordType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('isEnabled', SwitchType::class, [
            'label' => 'user.form.enabled',
            'required' => false
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteUser::class,
        ]);
    }

}