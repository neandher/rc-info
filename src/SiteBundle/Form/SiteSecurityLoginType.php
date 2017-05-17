<?php

namespace SiteBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\Type\SecurityLoginType;

class SiteSecurityLoginType extends SecurityLoginType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->remove('_username')
            ->add('_username', TextType::class, [
                'label' => 'user.form.cnpj',
            ]);
    }
}