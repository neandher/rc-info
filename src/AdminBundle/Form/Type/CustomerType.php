<?php

namespace AdminBundle\Form\Type;

use SiteBundle\Entity\Customer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
            ->add('cnpj', TextType::class, [
                'label' => 'admin.customers.fields.cnpj',
                'attr' => ['class' => "mask_cnpj"]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'admin.customers.fields.phone_number',
                'required' => false,
                'attr' => ['class' => 'mask_phone']
            ])
            ->add('billPayDay', IntegerType::class, [
                'label' => 'admin.customers.fields.billPayDay',
            ])
            ->add('billAmount', MoneyCustomType::class, [
                'label' => 'admin.customers.fields.billAmount',
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
            ])
            ->add('url', UrlType::class, [
                'label' => 'admin.customers.fields.url',
                'attr' => ['class' => 'form-control']
            ])
            ->add('imageFile', FileType::class, ['label' => 'admin.customers.fields.image'])
            ->add('text', TextareaType::class, [
                'label' => 'admin.customers.fields.text',
                'attr' => ['rows' => '10']
            ])
            ->add('publishedAt', DateTimePickerType::class, [
                'label' => 'admin.customers.fields.published_at',
                'attr' => ['readonly' => true, 'class' => 'form_datetime']
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