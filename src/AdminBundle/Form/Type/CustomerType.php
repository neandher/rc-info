<?php

namespace AdminBundle\Form\Type;

use SiteBundle\Entity\Customer;
use SiteBundle\Form\CustomerAddressesType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use UserBundle\Form\Type\PlainPasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends PlainPasswordType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'admin.customers.fields.name'])
            ->add('email', EmailType::class, ['label' => 'admin.customers.fields.email'])
            ->add('cnpj', TextType::class, ['label' => 'admin.customers.fields.cnpj'])
            ->add('phoneNumber', TextType::class, [
                'label' => 'admin.customers.fields.phone_number',
                'required' => false
            ])
            ->add('siteUser', SiteUserType::class, ['label' => false])
            ->add('customerAddresses', CollectionType::class, [
                'entry_type' => CustomerAddressesType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'admin.customerAddresses.title',
                'label_attr' => ['class' => 'hide'],
                'attr' => ['class' => 'hide'],
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'validation_groups' => ['Default', 'creating'],
            'is_edit' => false,
        ]);
    }

}