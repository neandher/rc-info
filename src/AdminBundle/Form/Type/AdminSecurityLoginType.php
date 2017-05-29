<?php

namespace AdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\Type\SecurityLoginType;

class AdminSecurityLoginType extends SecurityLoginType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->remove('_remember_me')
            ->add('_remember_me', MtCheckboxType::class, [
                'label' => 'user.form.remember_me'
            ]);
    }
}