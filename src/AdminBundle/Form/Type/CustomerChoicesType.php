<?php

namespace AdminBundle\Form\Type;

use SiteBundle\Entity\Customer;
use SiteBundle\Repository\CustomerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerChoicesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'query_builder' => function (CustomerRepository $er) {
                    return $er->queryLatestForm();
                },
                'choice_label' => 'name',
                'label' => 'admin.customers.title'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'inherit_data' => true
            ]
        );
    }

}
